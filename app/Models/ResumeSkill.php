<?php

namespace App\Models;

use Database\Factories\ResumeSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ResumeSkill extends Pivot
{
    /** @use HasFactory<ResumeSkillFactory> */
    use HasFactory;

    protected $table = 'resume_skills';

    protected $fillable = [
        'resume_id',
        'skill_id',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];
}
