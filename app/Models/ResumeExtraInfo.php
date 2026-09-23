<?php

namespace App\Models;

use Database\Factories\ResumeExtraInfoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeExtraInfo extends Model
{
    /** @use HasFactory<ResumeExtraInfoFactory> */
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'title',
        'value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
