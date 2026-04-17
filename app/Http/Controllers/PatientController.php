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
        $query = Patient::with(['vaccines', 'vaccineSchedules']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('pid', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by vaccine type
        if ($request->filled('jenis_vaksin')) {
            $query->whereHas('vaccines.vaccineType', function ($q) use ($request) {
                $q->where('nama_vaksin', $request->input('jenis_vaksin'));
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');
        
        $allowedSortFields = ['pid', 'nama_pasien', 'no_hp', 'alamat', 'dob'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sorting: PID ascending
            $query->orderBy('pid', 'asc');
        }

        $patients = $query->paginate(30)
            ->withQueryString();

        // Stats for vaccine types
        $stats = [
            'total_hpv' => Patient::whereHas('vaccines.vaccineType', function ($q) {
                $q->where('nama_vaksin', 'HPV');
            })->count(),
            'total_influenza' => Patient::whereHas('vaccines.vaccineType', function ($q) {
                $q->where('nama_vaksin', 'Influenza');
            })->count(),
            'total_hepatitis' => Patient::whereHas('vaccines.vaccineType', function ($q) {
                $q->where('nama_vaksin', 'Hepatitis');
            })->count(),
        ];

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
            Log::info("Patient {$id} deleted successfully");

            return redirect()->route('patients.index')
                ->with('success', 'Data pasien berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete patient {$id}: " . $e->getMessage());

            return redirect()->route('patients.index')
                ->with('error', 'Gagal menghapus data pasien: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $jenisVaksin = $request->input('jenis_vaksin');
        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');

        $filename = 'data_pasien_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        Log::info("Exporting patients to Excel", [
            'search' => $search,
            'jenis_vaksin' => $jenisVaksin,
            'sort' => $sortField,
            'direction' => $sortDirection,
        ]);

        return Excel::download(
            new PatientsExport($search, $jenisVaksin, $sortField, $sortDirection),
            $filename
        );
    }

    public function exportPDF(Request $request)
    {
        $query = Patient::with(['vaccines.vaccineType']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('pid', 'like', "%{$search}%")
                  ->orWhere('nama_pasien', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by vaccine type
        if ($request->filled('jenis_vaksin')) {
            $query->whereHas('vaccines.vaccineType', function ($q) use ($request) {
                $q->where('nama_vaksin', $request->input('jenis_vaksin'));
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'pid');
        $sortDirection = $request->input('direction', 'asc');
        
        $allowedSortFields = ['pid', 'nama_pasien', 'no_hp', 'alamat', 'dob'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('pid', 'asc');
        }

        $patients = $query->get();

        Log::info("Exporting patients to PDF", [
            'count' => $patients->count(),
            'search' => $request->input('search'),
            'jenis_vaksin' => $request->input('jenis_vaksin'),
        ]);

        $pdf = Pdf::loadView('patients.pdf', [
            'patients' => $patients,
            'filters' => [
                'search' => $request->input('search'),
                'jenis_vaksin' => $request->input('jenis_vaksin'),
            ],
            'exported_at' => now()->format('d-m-Y H:i:s'),
        ]);

        $filename = 'data_pasien_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
