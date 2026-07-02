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
        'kekuatan',
        'satuan_kekuatan',
        'dosis',
        'waktu_minum',
        'makan',
        'jumlah',
        'satuan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    protected $attributes = [
        'waktu_minum' => 'Sesuai Dosis',
        'makan'       => '-',
        'jumlah'      => 0,
        'satuan'           => '-',
        'satuan_kekuatan'  => 'mg',
    ];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }
}
