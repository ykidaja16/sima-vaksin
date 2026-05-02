<?php

namespace App\Http\Controllers;

use App\Exports\PatientsExport;
use App\Models\Patient;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery();

        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        $patients = $query->paginate(30)->withQueryString();

        $stats = $this->getStats();
        $vaccineTypes = \App\Models\VaccineType::where('is_active', true)->orderBy('nama_vaksin')->get();

        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');

        return view('patients.index', compact('patients', 'stats', 'vaccineTypes', 'sortField', 'sortDirection'));
    }

public function show($id)
    {
        $patient = Patient::with(['vaccines.schedules'])->findOrFail($id);
        $vaccineTypes = \App\Models\VaccineType::where('is_active', true)->orderBy('nama_vaksin')->get();
        return view('patients.show', compact('patient', 'vaccineTypes'));
    }

    public function edit($id)
    {
        $patient = Patient::with(['vaccines.vaccineType'])->findOrFail($id);
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('nama_cabang')->get();
        $vaccineTypes = \App\Models\VaccineType::where('is_active', true)->orderBy('nama_vaksin')->get();
        
        return view('patients.edit', compact('patient', 'branches', 'vaccineTypes'));
    }

    public function update(Request $request, $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'pid' => 'required|string|max:50',
            'nama_pasien' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'dob' => 'required|date',
        ], [
            'branch_id.required' => 'Pilih cabang terlebih dahulu',
            'pid.required' => 'PID wajib diisi',
            'nama_pasien.required' => 'Nama pasien wajib diisi',
            'no_hp.required' => 'No HP wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'dob.required' => 'Tanggal lahir wajib diisi',
            'dob.date' => 'Format tanggal lahir tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($id);
            
            // Validasi PID prefix
            $branch = \App\Models\Branch::findOrFail($request->branch_id);
            $prefix = strtoupper(substr($request->pid, 0, 2));
            $expectedPrefix = strtoupper($branch->kode_prefix);
            
            if ($prefix !== $expectedPrefix) {
                return redirect()->back()
                    ->with('error', "PID harus diawali dengan prefix cabang: {$branch->kode_prefix}")
                    ->withInput();
            }

            // Cek duplikat PID (kecuali untuk patient ini sendiri)
            $existingPatient = Patient::where('pid', $request->pid)
                ->where('id', '!=', $id)
                ->first();
            if ($existingPatient) {
                return redirect()->back()
                    ->with('error', "PID {$request->pid} sudah digunakan oleh pasien lain")
                    ->withInput();
            }

            // Update patient
            $patient->update([
                'branch_id' => $request->branch_id,
                'pid' => $request->pid,
                'nama_pasien' => $request->nama_pasien,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'dob' => $request->dob,
            ]);

            DB::commit();

            Log::info("Patient updated: {$patient->pid} - {$patient->nama_pasien}");

            return redirect()->route('patients.index')
                ->with('success', 'Data pasien berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Update patient gagal: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal mengupdate data pasien: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($id);
            $patient->delete();

            DB::commit();

            return redirect()->route('patients.index')
                ->with('success', 'Data pasien berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Delete patient gagal: " . $e->getMessage());

            return redirect()->route('patients.index')
                ->with('error', 'Gagal menghapus data pasien');
        }
    }

    public function exportExcel(Request $request)
    {
        $filename = 'data_pasien_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new PatientsExport(
                $request->input('search'),
                $request->input('jenis_vaksin'),
                $request->input('sort', 'pid'),
                $request->input('direction', 'asc')
            ),
            $filename
        );
    }

    public function exportPDF(Request $request)
    {
        $query = $this->baseQuery();

        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        // Limit biar aman (hindari memory jebol)
        $patients = $query->limit(1000)->get();

        Log::info("Export PDF", [
            'count' => $patients->count()
        ]);

        $pdf = Pdf::loadView('patients.pdf', [
            'patients' => $patients,
            'filters' => [
                'search' => $request->input('search'),
                'jenis_vaksin' => $request->input('jenis_vaksin'),
            ],
            'exported_at' => now()->format('d-m-Y H:i:s'),
        ]);

        return $pdf->download('data_pasien_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * 🔹 BASE QUERY (biar tidak duplikat)
     */
    private function baseQuery()
    {
        return Patient::with(['vaccines.vaccineType', 'vaccines.schedules']);
    }

    /**
     * 🔹 FILTER
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('pid', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_vaksin')) {
            $query->whereHas('vaccines.vaccineType', function ($q) use ($request) {
                $q->where('nama_vaksin', $request->input('jenis_vaksin'));
            });
        }

        return $query;
    }

    /**
     * 🔹 SORTING (AMAN)
     */
    private function applySorting($query, Request $request)
    {
        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');

        $allowed = ['pid', 'nama_pasien', 'no_hp', 'alamat', 'dob'];

        if (!in_array($sortField, $allowed)) {
            $sortField = 'pid';
        }

        return $query->orderBy($sortField, $sortDirection);
    }

    /**
     * 🔹 STATS (OPTIMIZED - 1 QUERY, DYNAMIC)
     */
public function updateVaccineFirstDate(Request $request, $id)
    {
        $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
            'vaccine_type_id' => 'required|exists:vaccine_types,id',
            'tanggal_vaksin_pertama' => 'required|date',
        ], [
            'vaccine_id.required' => 'Vaccine ID wajib dipilih',
            'vaccine_id.exists' => 'Vaccine tidak ditemukan',
            'vaccine_type_id.required' => 'Jenis vaksin wajib dipilih',
            'vaccine_type_id.exists' => 'Jenis vaksin tidak ditemukan',
            'tanggal_vaksin_pertama.required' => 'Tanggal dosis pertama wajib diisi',
            'tanggal_vaksin_pertama.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $vaccine = Vaccine::where('id', $request->vaccine_id)
                ->where('patient_id', $id)
                ->firstOrFail();

            // Update vaccine_type_id jika berbeda
            $vaccine->vaccine_type_id = $request->vaccine_type_id;
            $vaccine->save();

            $newDate = Carbon::parse($request->tanggal_vaksin_pertama);

            $service = new \App\Services\VaccineScheduleService();
            $service->updateSchedulesByFirstDate($vaccine, $newDate);

            Log::info("Updated vaccine type and first date for vaccine {$vaccine->id}: type={$request->vaccine_type_id}, date={$newDate->format('Y-m-d')}");

            return redirect()->back()->with('success', 'Jadwal vaksinasi berhasil diupdate');

        } catch (\Exception $e) {
            Log::error("Update vaccine first date gagal: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

private function getStats()
    {
        $raw = Patient::join('vaccines', 'patients.id', '=', 'vaccines.patient_id')
            ->join('vaccine_types', 'vaccines.vaccine_type_id', '=', 'vaccine_types.id')
            ->where('vaccine_types.is_active', true)
            ->selectRaw('vaccine_types.id as vaccine_type_id, COUNT(DISTINCT patients.id) as total')
            ->groupBy('vaccine_types.id')
            ->pluck('total', 'vaccine_type_id')
            ->toArray();

        return $raw;
    }

    public function destroyVaccine(Request $request, $id)
    {
        $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
        ], [
            'vaccine_id.required' => 'Vaccine ID wajib dipilih',
            'vaccine_id.exists' => 'Vaccine tidak ditemukan',
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $vaccine = Vaccine::where('id', $request->vaccine_id)
                ->where('patient_id', $id)
                ->firstOrFail();

            // Cek apakah ini vaccine terakhir
            $vaccineCount = Vaccine::where('patient_id', $id)->count();
            if ($vaccineCount <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus vaccine terakhir');
            }

            // Hapus schedules dulu
            $vaccine->schedules()->delete();

            // Hapus vaccine
            $vaccine->delete();

            Log::info("Deleted vaccine {$request->vaccine_id} for patient {$id}");

            return redirect()->back()->with('success', 'Vaccine berhasil dihapus');

        } catch (\Exception $e) {
            Log::error("Delete vaccine gagal: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus vaccine: ' . $e->getMessage());
        }
    }
}
