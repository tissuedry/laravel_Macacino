<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Baris 'use Laravel\Sanctum\HasApiTokens;' sudah dihapus dari sini

class User extends Authenticatable
{
    // 'HasApiTokens' juga sudah dihapus dari baris di bawah ini
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }
}