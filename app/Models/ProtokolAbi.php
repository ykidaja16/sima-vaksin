<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProtokolAbi extends Model
{
    protected $table = 'protokol_abi';

    protected $fillable = [
        'no_protokol',
        'user_id',
        'nama_dokter',
        'nama_pasien',
        'umur',
        'alamat',
        'tanggal_pemeriksaan',
        'right_arm_sistolik',
        'right_arm_diastolik',
        'right_arm_mean',
        'left_arm_sistolik',
        'left_arm_diastolik',
        'left_arm_mean',
        'right_ankle_sistolik',
        'right_ankle_diastolik',
        'right_ankle_mean',
        'left_ankle_sistolik',
        'left_ankle_diastolik',
        'left_ankle_mean',
        'highest_brachial_sistolik',
        'abi_left_pembilang',
        'abi_left_penyebut',
        'abi_left_hasil',
        'abi_right_pembilang',
        'abi_right_penyebut',
        'abi_right_hasil',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'umur' => 'integer',
        'abi_left_hasil' => 'decimal:2',
        'abi_right_hasil' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNoProtokol(): string
    {
        $last = static::lockForUpdate()
            ->where('no_protokol', 'like', 'ABI%')
            ->orderByDesc('id')
            ->first();

        $nextNum = $last ? ((int) substr($last->no_protokol, 3)) + 1 : 1;

        return 'ABI' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }
}
