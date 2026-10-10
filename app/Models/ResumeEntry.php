<?php

namespace App\Models;

use App\Enums\ResumeEntryType;
use Database\Factories\ResumeEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeEntry extends Model
{
    /** @use HasFactory<ResumeEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'organization',
        'location',
        'description',
        'start_date',
        'end_date',
        'in_progress',
        'resume_id',
        'order',
        'type',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'in_progress' => 'boolean',
        'type' => ResumeEntryType::class,
    ];

    protected $attributes = [
        'in_progress' => false,
    ];
}
