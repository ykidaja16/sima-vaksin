<?php

namespace App\Http\Controllers;

use App\Exports\PatientsExport;
use App\Models\Patient;
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
        return view('patients.show', compact('patient'));
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
}
