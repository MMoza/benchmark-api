<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $fillable = [
        'name',
        'description',
        'frequency',
        'target_count',
        'completed_count',
    ];

    protected $casts = [
        'target_count'    => 'integer',
        'completed_count' => 'integer',
    ];
}
