<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepObat extends Model
{
    protected $table = 'resep_obat';

    protected $fillable = [
        'resep_id',
        'nama_obat',
        'dosis',
        'waktu_minum',
        'makan',
    ];

    protected $attributes = [
        'waktu_minum' => 'Sesuai Dosis',
        'makan'       => '-',
    ];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }
}
