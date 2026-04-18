<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Vaccine;
use App\Models\VaccineType;
use App\Services\VaccineScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManualInputController extends Controller
{
    /**
     * Menampilkan form input manual
     */
    public function index()
    {
        $branches = Branch::where('is_active', true)->orderBy('nama_cabang')->get();
        $vaccineTypes = VaccineType::where('is_active', true)->orderBy('nama_vaksin')->get();
        
        // Ambil data temporary dari session
        $temporaryData = session()->get('manual_input_data', []);
        
        return view('manual-input.index', compact('branches', 'vaccineTypes', 'temporaryData'));
    }

    /**
     * Menambahkan data ke session temporary (AJAX)
     */
    public function addToSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'pid' => 'required|string|max:50',
            'nama_pasien' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'dob' => 'required|date',
            'jenis_vaksin' => 'required|exists:vaccine_types,nama_vaksin',
            'tanggal_vaksin_pertama' => 'required|date',
        ], [
            'branch_id.required' => 'Pilih cabang terlebih dahulu',
            'pid.required' => 'PID wajib diisi',
            'nama_pasien.required' => 'Nama pasien wajib diisi',
            'no_hp.required' => 'No HP wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'dob.required' => 'Tanggal lahir wajib diisi',
            'dob.date' => 'Format tanggal lahir tidak valid',
            'jenis_vaksin.required' => 'Jenis vaksin wajib dipilih',
            'jenis_vaksin.exists' => 'Jenis vaksin tidak valid',
            'tanggal_vaksin_pertama.required' => 'Tanggal vaksin pertama wajib diisi',
            'tanggal_vaksin_pertama.date' => 'Format tanggal vaksin pertama tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $branch = Branch::findOrFail($request->branch_id);
            
            // Validasi PID prefix
            $prefix = strtoupper(substr($request->pid, 0, 2));
            $expectedPrefix = strtoupper($branch->kode_prefix);
            
            if ($prefix !== $expectedPrefix) {
                return response()->json([
                    'success' => false,
                    'errors' => ["PID harus diawali dengan prefix cabang: {$branch->kode_prefix}"]
                ], 422);
            }

            // Cek duplikat PID di database
            $existingPatient = Patient::where('pid', $request->pid)->first();
            if ($existingPatient) {
                return response()->json([
                    'success' => false,
                    'errors' => ["PID {$request->pid} sudah ada di database dengan nama {$existingPatient->nama_pasien}"]
                ], 422);
            }

            // Ambil data temporary yang sudah ada
            $temporaryData = session()->get('manual_input_data', []);
            
            // Cek duplikat di temporary data
            foreach ($temporaryData as $data) {
                if ($data['pid'] === $request->pid) {
                    return response()->json([
                        'success' => false,
                        'errors' => ["PID {$request->pid} sudah ada di daftar input"]
                    ], 422);
                }
            }

            // Ambil vaccine type
            $vaccineType = VaccineType::where('nama_vaksin', $request->jenis_vaksin)->first();

            // Tambahkan data baru ke temporary
            $newData = [
                'id' => uniqid('temp_'),
                'branch_id' => $request->branch_id,
                'branch_name' => $branch->nama_cabang,
                'pid' => $request->pid,
                'nama_pasien' => $request->nama_pasien,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'dob' => $request->dob,
                'jenis_vaksin' => $request->jenis_vaksin,
                'vaccine_type_id' => $vaccineType->id,
                'tanggal_vaksin_pertama' => $request->tanggal_vaksin_pertama,
            ];

            $temporaryData[] = $newData;
            
            // Simpan ke session
            session()->put('manual_input_data', $temporaryData);

            Log::info("Data added to manual input session: {$request->pid} - {$request->nama_pasien}");

            return response()->json([
                'success' => true,
                'data' => $newData,
                'count' => count($temporaryData)
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to add data to session: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['Gagal menambahkan data: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Menyimpan semua data dari session ke database
     */
    public function save(Request $request)
    {
        $temporaryData = session()->get('manual_input_data', []);
        
        if (empty($temporaryData)) {
            return redirect()->route('manual-input.index')
                ->with('error', 'Tidak ada data untuk disimpan. Silakan tambahkan data terlebih dahulu.');
        }

        try {
            $importedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($temporaryData as $data) {
                try {
                    // Cek ulang duplikat PID di database
                    $existingPatient = Patient::where('pid', $data['pid'])->first();
                    if ($existingPatient) {
                        $errors[] = "PID {$data['pid']} sudah ada di database, dilewati";
                        continue;
                    }

                    // 1. Buat Patient
                    $patient = Patient::create([
                        'branch_id' => $data['branch_id'],
                        'pid' => $data['pid'],
                        'nama_pasien' => $data['nama_pasien'],
                        'no_hp' => $data['no_hp'],
                        'alamat' => $data['alamat'],
                        'dob' => $data['dob'],
                    ]);

                    // 2. Buat Vaccine
                    $vaccine = Vaccine::create([
                        'patient_id' => $patient->id,
                        'vaccine_type_id' => $data['vaccine_type_id'],
                        'tanggal_vaksin_pertama' => $data['tanggal_vaksin_pertama'],
                    ]);

                    // 3. Generate jadwal vaksin
                    $scheduleService = new VaccineScheduleService();
                    $scheduleService->generateSchedules($vaccine);

                    $importedCount++;
                    Log::info("Manual input saved: {$data['pid']} - {$data['nama_pasien']}");

                } catch (\Exception $e) {
                    $errors[] = "Error pada {$data['pid']}: " . $e->getMessage();
                    Log::error("Error saving manual data for {$data['pid']}: " . $e->getMessage());
                }
            }

            if ($importedCount === 0) {
                DB::rollBack();
                return redirect()->route('manual-input.index')
                    ->with('error', 'Tidak ada data yang berhasil disimpan. ' . implode(', ', $errors));
            }

            DB::commit();

            // Clear session
            session()->forget('manual_input_data');

            $message = "{$importedCount} data berhasil disimpan.";
            if (!empty($errors)) {
                $message .= " Ada " . count($errors) . " error.";
            }

            Log::info("Manual input completed: {$importedCount} data saved");

            return redirect()->route('patients.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Manual input failed: " . $e->getMessage());
            
            return redirect()->route('manual-input.index')
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus satu item dari session (AJAX)
     */
    public function remove($id)
    {
        $temporaryData = session()->get('manual_input_data', []);
        
        // Filter out the item with matching id
        $temporaryData = array_filter($temporaryData, function($item) use ($id) {
            return $item['id'] !== $id;
        });
        
        // Re-index array
        $temporaryData = array_values($temporaryData);
        
        session()->put('manual_input_data', $temporaryData);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => count($temporaryData)
            ]);
        }
        
        return redirect()->route('manual-input.index')
            ->with('success', 'Data berhasil dihapus dari daftar');
    }

    /**
     * Menghapus semua data dari session (AJAX)
     */
    public function clear()
    {
        session()->forget('manual_input_data');
        
        Log::info("Manual input session cleared");
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true
            ]);
        }
        
        return redirect()->route('manual-input.index')
            ->with('success', 'Daftar input berhasil dikosongkan');
    }
}
