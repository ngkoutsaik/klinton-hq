<?php

namespace App\Models;

use Database\Factories\ResumeLinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeLink extends Model
{
    /** @use HasFactory<ResumeLinkFactory> */
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'link_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];
}
