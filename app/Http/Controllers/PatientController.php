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

        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');

        return view('patients.index', compact('patients', 'stats', 'sortField', 'sortDirection'));
    }

    public function show($id)
    {
        $patient = Patient::with(['vaccines.schedules'])->findOrFail($id);
        return view('patients.show', compact('patient'));
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
        return Patient::with(['vaccines.vaccineType', 'vaccineSchedules']);
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
     * 🔹 STATS (OPTIMIZED - 1 QUERY)
     */
    private function getStats()
    {
        $raw = Patient::join('vaccines', 'patients.id', '=', 'vaccines.patient_id')
            ->join('vaccine_types', 'vaccines.vaccine_type_id', '=', 'vaccine_types.id')
            ->whereIn('vaccine_types.nama_vaksin', ['HPV', 'Influenza', 'Hepatitis'])
            ->selectRaw('vaccine_types.nama_vaksin, COUNT(DISTINCT patients.id) as total')
            ->groupBy('vaccine_types.nama_vaksin')
            ->pluck('total', 'nama_vaksin');

        return [
            'total_hpv' => $raw['HPV'] ?? 0,
            'total_influenza' => $raw['Influenza'] ?? 0,
            'total_hepatitis' => $raw['Hepatitis'] ?? 0,
        ];
    }
}