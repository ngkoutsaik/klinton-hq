<?php

namespace App\Models;

use Database\Factories\ResumeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    /** @use HasFactory<ResumeFactory> */
    use HasFactory;

    protected $fillable = [
        'role',
        'company_name',
        'location',
        'description',
        'start_date',
        'end_date',
        'in_progress',
    ];

    protected $casts = [
        'in_progress' => 'boolean',
    ];
}
