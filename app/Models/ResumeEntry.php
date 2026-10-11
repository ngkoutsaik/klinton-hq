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
        'start_year',
        'start_month',
        'end_year',
        'end_month',
        'in_progress',
        'resume_id',
        'order',
        'type',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'start_month' => 'integer',
        'end_year' => 'integer',
        'end_month' => 'integer',
        'in_progress' => 'boolean',
        'type' => ResumeEntryType::class,
    ];

    protected $attributes = [
        'in_progress' => false,
    ];

    protected static function booted(): void
    {
        static::saving(function (ResumeEntry $entry) {
            if ($entry->in_progress) {
                $entry->end_year = null;
                $entry->end_month = null;
            }
        });
    }
}
