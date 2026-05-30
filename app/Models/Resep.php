<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'no_resep',
        'user_id',
        'nama_dokter',
        'nama_pasien',
        'umur',
        'alamat',
        'tanggal_resep',
    ];

    protected $casts = [
        'tanggal_resep' => 'date',
        'umur' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function obat(): HasMany
    {
        return $this->hasMany(ResepObat::class);
    }

    public static function generateNoResep(): string
    {
        $last = static::lockForUpdate()
            ->where('no_resep', 'like', 'RSP%')
            ->orderByDesc('id')
            ->first();

        $nextNum = $last ? ((int) substr($last->no_resep, 3)) + 1 : 1;

        return 'RSP' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }
}
