<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaccineType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_vaksin',
        'deskripsi',
        'total_dosis',
        'interval_bulan',
        'is_active',
    ];

    protected $casts = [
        'total_dosis' => 'integer',
        'interval_bulan' => 'array',
        'is_active' => 'boolean',
    ];

    public function vaccines(): HasMany
    {
        return $this->hasMany(Vaccine::class);
    }

    public function getIntervalBulanAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return json_decode($value, true) ?? [0];
        }
        
        return [0];
    }

    public function getTotalDosisAttribute($value): int
    {
        return (int) ($value ?? 1);
    }
}
