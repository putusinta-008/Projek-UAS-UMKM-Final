<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ⬅️ WAJIB
    ];

    /**
     * Kolom yang disembunyikan
     */
    protected $hidden = [
        'password',
    ];

    /**
     * JWT identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT custom claims
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}