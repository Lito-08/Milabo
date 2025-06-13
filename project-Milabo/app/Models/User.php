<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'staff' or 'student'
    ];

    // User has many folders
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }
    
    // User has many files
    public function files()
    {
        return $this->hasMany(File::class);
    }
}