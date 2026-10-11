<?php

namespace App\Models;

use Database\Factories\ResumeSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ResumeSkill extends Pivot
{
    /** @use HasFactory<ResumeSkillFactory> */
    use HasFactory;

    protected $table = 'resume_skills';

    public $incrementing = true;

    protected $fillable = [
        'resume_id',
        'skill_id',
        'is_active',
        'order',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * @return BelongsTo<Skill, $this>
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
