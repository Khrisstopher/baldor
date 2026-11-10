<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    /** @use HasFactory<\Database\Factories\ExerciseFactory> */
    use HasFactory;

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
}
