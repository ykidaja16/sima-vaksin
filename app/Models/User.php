<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isIT(): bool
    {
        return $this->role?->nama_role === 'IT';
    }

    public function isAdmin(): bool
    {
        return $this->role?->nama_role === 'Admin';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role?->nama_role ?? 'Unknown';
    }
}
