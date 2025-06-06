<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
