<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Vaccine;
use App\Models\VaccineType;
use App\Services\VaccineScheduleService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Throwable;

class PatientsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    private Branch $branch;
    private int $importedCount = 0;
    private int $skippedCount = 0;
    private int $duplicateCount = 0; // Count data that already exists in database
    private array $errors = [];
    private int $rowNumber = 0;
    private array $processedRows = []; // Track duplicates within the file
    private array $validatedRows = []; // Store validated data for second phase

    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    public function collection(Collection $rows)
    {
        $scheduleService = new VaccineScheduleService();

        // PHASE 1: Validate all rows first without saving
        $this->validateAllRows($rows);

        // If there are validation errors, throw exception with all details to trigger rollback
        if (!empty($this->errors)) {
            $errorMessage = "Validasi gagal: " . count($this->errors) . " baris memiliki error:\n";
            $errorMessage .= implode("\n", $this->errors);
            throw new \Exception($errorMessage);
        }

        // PHASE 2: All validations passed, now save to database
        // This is wrapped in a transaction in the controller
        foreach ($this->validatedRows as $rowData) {
            $this->rowNumber = $rowData['rowNumber'];
            
            try {
                $pid = $rowData['pid'];
                $namaPasien = $rowData['nama_pasien'];
                $vaccineType = $rowData['vaccine_type'];
                $tanggalVaksin = $rowData['tanggal_vaksin'];
                $dob = $rowData['dob'];
                $row = $rowData['original_row'];

                // Create or update patient
                $patient = Patient::updateOrCreate(
                    [
                        'branch_id' => $this->branch->id,
                        'pid' => $pid,
                    ],
                    [
                        'nama_pasien' => $namaPasien,
                        'no_hp' => $row['no_hp'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                        'dob' => $dob,
                    ]
                );

                // Check if vaccine already exists for this patient and type with same date
                $existingVaccine = Vaccine::where('patient_id', $patient->id)
                    ->where('vaccine_type_id', $vaccineType->id)
                    ->first();

                if ($existingVaccine) {
                    // Check if same date - this is a duplicate
                    if ($existingVaccine->tanggal_vaksin_pertama->format('Y-m-d') === $tanggalVaksin->format('Y-m-d')) {
                        // Duplicate: same patient, same vaccine type, same date
                        $this->duplicateCount++;
                        Log::info("Row {$this->rowNumber}: Duplicate detected - vaccine already exists with same date", [
                            'patient_id' => $patient->id,
                            'vaccine_id' => $existingVaccine->id,
                            'vaccine_type' => $vaccineType->nama_vaksin,
                        ]);
                    } else {
                        // Update existing vaccine date if different
                        $existingVaccine->update([
                            'tanggal_vaksin_pertama' => $tanggalVaksin,
                        ]);
                        // Delete old schedules and regenerate
                        $existingVaccine->schedules()->delete();
                        $scheduleService->generateSchedules($existingVaccine);
                        Log::info("Row {$this->rowNumber}: Updated existing vaccine and regenerated schedules", [
                            'patient_id' => $patient->id,
                            'vaccine_id' => $existingVaccine->id,
                            'vaccine_type' => $vaccineType->nama_vaksin,
                        ]);
                    }
                } else {
                    // Create new vaccine record
                    $vaccine = Vaccine::create([
                        'patient_id' => $patient->id,
                        'vaccine_type_id' => $vaccineType->id,
                        'tanggal_vaksin_pertama' => $tanggalVaksin,
                    ]);

                    // Generate schedules
                    $scheduleService->generateSchedules($vaccine);
                    
                    Log::info("Row {$this->rowNumber}: Created new vaccine and generated schedules", [
                        'patient_id' => $patient->id,
                        'vaccine_id' => $vaccine->id,
                        'vaccine_type' => $vaccineType->nama_vaksin,
                    ]);
                }

                $this->importedCount++;

            } catch (\Exception $e) {
                // If any error occurs during save, throw to trigger rollback
                throw new \Exception("Baris {$this->rowNumber}: Error saat menyimpan - " . $e->getMessage());
            }
        }
    }

    /**
     * Phase 1: Validate all rows without saving
     */
    private function validateAllRows(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->rowNumber = $index + 2; // +2 because Excel rows start at 1 and we have header row

            // Validate required fields
            if (empty($row['pid']) || empty($row['nama_pasien']) || empty($row['jenis_vaksin']) || empty($row['tanggal_vaksin_pertama'])) {
                $this->errors[] = "Baris {$this->rowNumber}: Kolom wajib tidak lengkap (pid, nama_pasien, jenis_vaksin, atau tanggal_vaksin_pertama)";
                continue;
            }

            $pid = trim($row['pid']);
            $namaPasien = trim($row['nama_pasien']);
            $jenisVaksinInput = trim($row['jenis_vaksin']);

            // Validate PID prefix matches branch
            $prefix = strtoupper(substr($pid, 0, 2));
            if ($prefix !== $this->branch->kode_prefix) {
                $this->errors[] = "Baris {$this->rowNumber}: PID '{$pid}' tidak sesuai dengan cabang {$this->branch->nama_cabang}. Harus diawali dengan '{$this->branch->kode_prefix}'";
                continue;
            }

            // Parse dates
            $tanggalVaksin = $this->parseDate($row['tanggal_vaksin_pertama']);
            $dob = !empty($row['dob']) ? $this->parseDate($row['dob']) : null;

            if (!$tanggalVaksin) {
                $this->errors[] = "Baris {$this->rowNumber}: Format tanggal vaksin pertama tidak valid: '{$row['tanggal_vaksin_pertama']}'";
                continue;
            }

            // Get vaccine type from database
            $vaccineType = VaccineType::where('nama_vaksin', 'LIKE', $jenisVaksinInput)
                ->where('is_active', true)
                ->first();
            
            if (!$vaccineType) {
                $this->errors[] = "Baris {$this->rowNumber}: Jenis vaksin '{$jenisVaksinInput}' tidak ditemukan di database";
                continue;
            }

            // Check for duplicate in current file (PID, Nama, DOB, Jenis Vaksin, Tanggal Vaksin Pertama)
            $duplicateKey = md5("{$pid}|{$namaPasien}|" . ($dob ? $dob->format('Y-m-d') : '') . "|{$vaccineType->id}|{$tanggalVaksin->format('Y-m-d')}");
            
            if (isset($this->processedRows[$duplicateKey])) {
                $this->errors[] = "Baris {$this->rowNumber}: Data duplikat dengan baris {$this->processedRows[$duplicateKey]} (PID, Nama, DOB, Jenis Vaksin, dan Tanggal Vaksin Pertama sama)";
                continue;
            }
            $this->processedRows[$duplicateKey] = $this->rowNumber;

            // Check if PID exists with different name in database
            $existingPatient = Patient::where('branch_id', $this->branch->id)
                ->where('pid', $pid)
                ->first();

            if ($existingPatient && $existingPatient->nama_pasien !== $namaPasien) {
                $this->errors[] = "Baris {$this->rowNumber}: PID '{$pid}' sudah digunakan oleh pasien dengan nama '{$existingPatient->nama_pasien}'";
                continue;
            }

            // Store validated data for phase 2
            $this->validatedRows[] = [
                'rowNumber' => $this->rowNumber,
                'pid' => $pid,
                'nama_pasien' => $namaPasien,
                'vaccine_type' => $vaccineType,
                'tanggal_vaksin' => $tanggalVaksin,
                'dob' => $dob,
                'original_row' => $row,
            ];
        }
    }

    public function rules(): array
    {
        return [
            'pid' => 'required|string|max:50',
            'nama_pasien' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'dob' => 'nullable',
            'jenis_vaksin' => 'required|string',
            'tanggal_vaksin_pertama' => 'required',
        ];
    }

    public function onError(Throwable $error): void
    {
        $this->errors[] = "Baris {$this->rowNumber}: " . $error->getMessage();
        Log::error("Import error on row {$this->rowNumber}: " . $error->getMessage());
    }

    /**
     * Parse date from various formats including Excel serial dates
     */
    private function parseDate($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        // Handle Excel serial date (numeric)
        if (is_numeric($date)) {
            $excelBaseDate = Carbon::create(1900, 1, 1);
            $days = (int) $date - 2;
            
            try {
                return $excelBaseDate->copy()->addDays($days);
            } catch (\Exception $e) {
                Log::warning("Failed to parse Excel serial date: {$date}");
                return null;
            }
        }

        // Try multiple date formats
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-M-Y', 'd M Y'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $date);
                if ($parsed && $parsed->year > 1900 && $parsed->year < 2100) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try generic parse as last resort
        try {
            $parsed = Carbon::parse($date);
            if ($parsed->year > 1900 && $parsed->year < 2100) {
                return $parsed;
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$date}");
        }

        return null;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getDuplicateCount(): int
    {
        return $this->duplicateCount;
    }
}
