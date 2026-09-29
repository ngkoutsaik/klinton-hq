<?php

namespace App\Models;

use Database\Factories\WorkExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    /** @use HasFactory<WorkExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'role_name',
        'company_name',
        'location',
        'description',
        'start_date',
        'end_date',
        'in_progress',
        'resume_id',
        'order',
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
