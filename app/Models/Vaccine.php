<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'vaccine_type_id',
        'tanggal_vaksin_pertama',
    ];

    protected $casts = [
        'tanggal_vaksin_pertama' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function vaccineType(): BelongsTo
    {
        return $this->belongsTo(VaccineType::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class);
    }

    public function getIntervalBulanAttribute(): array
    {
        return $this->vaccineType?->interval_bulan ?? [0];
    }

    public function getTotalDosisAttribute(): int
    {
        return $this->vaccineType?->total_dosis ?? 1;
    }

    public function getJenisVaksinAttribute(): string
    {
        return $this->vaccineType?->nama_vaksin ?? 'Unknown';
    }

    public function isDosisLengkap(): bool
    {
        $totalDosis = $this->getTotalDosisAttribute();
        $schedules = $this->schedules;
        
        $dosisDiterima = $schedules->count();
        if ($dosisDiterima < $totalDosis) {
            return false;
        }
        
        // Cek apakah tanggal vaksin terakhir sudah lewat
        $lastSchedule = $schedules->sortByDesc('tanggal_vaksin')->first();
        if (!$lastSchedule) {
            return false;
        }
        
        return $lastSchedule->tanggal_vaksin->lte(now());
    }



}
