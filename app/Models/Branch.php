<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'kode_prefix',
        'alamat',
        'no_telp',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
