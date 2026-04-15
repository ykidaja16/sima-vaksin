<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'pid',
        'nama_pasien',
        'no_hp',
        'alamat',
        'dob',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function vaccines(): HasMany
    {
        return $this->hasMany(Vaccine::class);
    }

    public function vaccineSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class)->orderBy('dosis_ke', 'asc');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob ? now()->diffInYears($this->dob) : null;
    }
}
