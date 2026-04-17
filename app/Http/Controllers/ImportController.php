<?php

namespace App\Http\Controllers;

use App\Imports\PatientsImport;
use App\Models\Branch;
use App\Models\VaccineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            
            // Wrap import in database transaction for all-or-nothing behavior
            $result = DB::transaction(function () use ($request, $branch) {
                $import = new PatientsImport($branch);
                
                Excel::import($import, $request->file('file'));

                return [
                    'importedCount' => $import->getImportedCount(),
                    'skippedCount' => $import->getSkippedCount(),
                    'duplicateCount' => $import->getDuplicateCount(),
                    'errors' => $import->getErrors(),
                ];
            });

            $importedCount = $result['importedCount'];
            $skippedCount = $result['skippedCount'];
            $duplicateCount = $result['duplicateCount'];
            $errors = $result['errors'];

            // If there are validation errors but import succeeded (shouldn't happen with new logic)
            if (!empty($errors)) {
                Log::warning("Excel import completed with warnings for branch {$branch->nama_cabang}: {$importedCount} imported, warnings: " . count($errors));
                return redirect()->route('import.index')
                    ->with('warning', "Import berhasil dengan {$importedCount} data, tetapi ada " . count($errors) . " peringatan.")
                    ->with('import_errors', $errors);
            }

            Log::info("Excel import completed successfully for branch {$branch->nama_cabang}: {$importedCount} imported, {$duplicateCount} duplicates");

            // Jika tidak ada data yang diimport (semua duplicate), beri notifikasi khusus
            if ($importedCount == 0 && $duplicateCount > 0) {
                return redirect()->route('patients.index')
                    ->with('warning', "Tidak ada data baru yang diimport. {$duplicateCount} data sudah ada di sistem.");
            }

            // Jika tidak ada data yang diimport dan tidak ada duplicate (file kosong atau semua error)
            if ($importedCount == 0 && $duplicateCount == 0) {
                return redirect()->route('patients.index')
                    ->with('warning', "Tidak ada data yang diimport.");
            }

            // Jika ada data yang diimport dan ada duplicate, beri notifikasi lengkap
            if ($duplicateCount > 0) {
                return redirect()->route('patients.index')
                    ->with('success', "Import berhasil! {$importedCount} data baru diimport. {$duplicateCount} data sudah ada di sistem (tidak diimport ulang).");
            }

            // Jika ada data yang diimport dan tidak ada duplicate, beri notifikasi sukses sederhana
            return redirect()->route('patients.index')
                ->with('success', "Import berhasil! {$importedCount} data berhasil diimport.");

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            Log::error("Excel validation failed: " . json_encode($errors));

            return redirect()->route('import.index')
                ->with('error', 'Validasi Excel gagal. Semua data dibatalkan.')
                ->with('validation_errors', $errors);

        } catch (\Exception $e) {
            Log::error("Excel import failed: " . $e->getMessage());
            
            // Check if this is a validation error from our custom validation
            if (str_contains($e->getMessage(), 'Validasi gagal')) {
                // Parse error message to get all details
                $errorLines = explode("\n", $e->getMessage());
                $errorSummary = array_shift($errorLines); // Remove first line (summary)
                
                // Clean up empty lines
                $detailErrors = array_filter($errorLines, function($line) {
                    return !empty(trim($line));
                });
                
                return redirect()->route('import.index')
                    ->with('error', 'Import dibatalkan! Terdapat ' . count($detailErrors) . ' baris yang gagal divalidasi. Semua data tidak disimpan.')
                    ->with('import_errors', $detailErrors);
            }
            
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
