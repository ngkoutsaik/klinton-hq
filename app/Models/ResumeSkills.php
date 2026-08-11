<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeSkills extends Model
{
    /** @use HasFactory<\Database\Factories\ResumeSkillsFactory> */
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'skill_id',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];
}
