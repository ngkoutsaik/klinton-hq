<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    /** @use HasFactory<\Database\Factories\WorkExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'start_date',
        'end_date',
        'in_progress',
        'resume_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'in_progress' => 'boolean',
    ];

    protected $attributes = [
        'in_progress' => false,
    ];
}
