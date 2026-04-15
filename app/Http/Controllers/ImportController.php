<?php

namespace App\Http\Controllers;

use App\Imports\PatientsImport;
use App\Models\Branch;
use App\Models\VaccineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $branches = Branch::where('is_active', true)->orderBy('nama_cabang')->get();
        $vaccineTypes = VaccineType::where('is_active', true)->orderBy('nama_vaksin')->get();
        return view('import.index', compact('branches', 'vaccineTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ], [
            'branch_id.required' => 'Pilih cabang terlebih dahulu',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $branch = Branch::findOrFail($request->input('branch_id'));
            $import = new PatientsImport($branch);
            
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $errors = $import->getErrors();

            $message = "Import berhasil! {$importedCount} data berhasil diimport.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} data dilewati.";
            }

            Log::info("Excel import completed for branch {$branch->nama_cabang}: {$importedCount} imported, {$skippedCount} skipped");

            if (!empty($errors)) {
                return redirect()->route('import.index')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->route('patients.index')
                ->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            Log::error("Excel validation failed: " . json_encode($errors));

            return redirect()->route('import.index')
                ->with('error', 'Validasi Excel gagal')
                ->with('validation_errors', $errors);

        } catch (\Exception $e) {
            Log::error("Excel import failed: " . $e->getMessage());
            
            return redirect()->route('import.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_pasien_vaksin.csv"',
        ];

        $content = "pid,nama_pasien,no_hp,alamat,dob,jenis_vaksin,tanggal_vaksin_pertama\n";
        $content .= "LXB001,Budi Santoso,08123456789,Jl. Mawar No. 1,1990-05-15,HPV,2026-01-15\n";
        $content .= "LXB002,Ani Wulandari,08234567890,Jl. Melati No. 2,1985-08-20,Hepatitis,2026-01-15\n";
        $content .= "LXB003,Cahyo Nugroho,08345678901,Jl. Anggrek No. 3,1992-12-10,Influenza,2026-01-15\n";

        return response($content, 200, $headers);
    }
}
