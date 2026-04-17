<?php

namespace App\Services;

// use App\Models\Patient;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use App\Models\VaccineType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VaccineScheduleService
{
    public function generateSchedules(Vaccine $vaccine): array
    {
        // Get intervals from vaccine type
        $vaccineType = $vaccine->vaccineType;
        $intervals = $vaccineType?->interval_bulan ?? [0];
        $schedules = [];
        $baseDate = Carbon::parse($vaccine->tanggal_vaksin_pertama);

        DB::beginTransaction();
        try {
            foreach ($intervals as $index => $interval) {
                $dosisKe = $index + 1;
                $scheduleDate = $baseDate->copy()->addMonths($interval);

                // Dosis pertama langsung completed, dosis berikutnya pending
                $status = ($dosisKe === 1) ? 'completed' : 'pending';

                $schedule = VaccineSchedule::create([
                    'patient_id' => $vaccine->patient_id,
                    'vaccine_id' => $vaccine->id,
                    'dosis_ke' => $dosisKe,
                    'tanggal_vaksin' => $scheduleDate,
                    'status' => $status,
                ]);

                $schedules[] = $schedule;
            }

            DB::commit();
            Log::info("Generated " . count($schedules) . " schedules for vaccine {$vaccine->id}");
            
            return $schedules;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate schedules: " . $e->getMessage());
            throw $e;
        }
    }

    public function getReminderH7(): array
    {
        $today = now()->startOfDay();
        $h7Date = now()->addDays(7)->endOfDay();

        $schedules = VaccineSchedule::with(['patient', 'vaccine.vaccineType'])
            ->pending()
            ->whereBetween('tanggal_vaksin', [$today, $h7Date])
            ->orderBy('tanggal_vaksin', 'asc')
            ->get();

        return $schedules->map(function ($schedule) {
            $daysUntil = now()->diffInDays($schedule->tanggal_vaksin, false);
            
            return [
                'schedule_id' => $schedule->id,
                'pid' => $schedule->patient->pid,
                'nama_pasien' => $schedule->patient->nama_pasien,
                'no_hp' => $schedule->patient->no_hp,
                'alamat' => $schedule->patient->alamat,
                'dob' => $schedule->patient->dob,
                'umur' => $schedule->patient->age,
                'jenis_vaksin' => $schedule->vaccine->jenis_vaksin,
                'dosis_ke' => $schedule->dosis_ke,
                'tanggal_vaksin' => $schedule->tanggal_vaksin,
                'days_until' => $daysUntil,
                'status' => $schedule->status,
            ];
        })->toArray();
    }

    public function completeSchedule(int $scheduleId, string $keterangan = null): bool
    {
        $schedule = VaccineSchedule::findOrFail($scheduleId);
        $schedule->markAsCompleted($keterangan);
        
        Log::info("Schedule {$scheduleId} marked as completed");
        
        return true;
    }

    public static function getVaccineTypes(): array
    {
        return VaccineType::where('is_active', true)
            ->pluck('nama_vaksin')
            ->toArray();
    }

    public static function getIntervals(string $jenisVaksin): array
    {
        $vaccineType = VaccineType::where('nama_vaksin', $jenisVaksin)
            ->where('is_active', true)
            ->first();
            
        return $vaccineType?->interval_bulan ?? [0];
    }

    public static function getTotalDoses(string $jenisVaksin): int
    {
        $vaccineType = VaccineType::where('nama_vaksin', $jenisVaksin)
            ->where('is_active', true)
            ->first();
            
        return $vaccineType?->total_dosis ?? 1;
    }
}
