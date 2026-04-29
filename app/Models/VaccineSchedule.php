<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccineSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'vaccine_id',
        'dosis_ke',
        'tanggal_vaksin',
        'status',
        'reminder_sent_at',
        'completed_at',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_vaksin' => 'date',
        'reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function markAsCompleted(?string $keterangan = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'keterangan' => $keterangan,
        ]);
    }

    public function markAsReminderSent(): void
    {
        $this->update([
            'reminder_sent_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReminderH7($query)
    {
        $today = now()->startOfDay();
        $h7Date = now()->addDays(7)->endOfDay();

        return $query->pending()
            ->whereBetween('tanggal_vaksin', [$today, $h7Date])
            ->orderBy('tanggal_vaksin', 'asc');
    }
}
