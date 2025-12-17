<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Definisikan Tipe Role biar konsisten
    const ROLE_ADMIN = 'admin';
    const ROLE_PRODUKSI = 'produksi';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Jangan lupa masukin ini biar bisa diinput
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Helper function buat ngecek role (optional tapi berguna)
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    
    public function isProduksi(): bool
    {
        return $this->role === self::ROLE_PRODUKSI;
    }
}