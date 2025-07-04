<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'parent_id', // For nested folders
    ];

    // Folder belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Folder has many files
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // Folder can have a parent folder
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // Folder can have many subfolders
    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }
}