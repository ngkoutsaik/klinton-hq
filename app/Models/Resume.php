<?php

namespace App\Models;

use Database\Factories\ResumeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    /** @use HasFactory<ResumeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'intro',
        'user_id',
        'published',
        'looking_for_role',
    ];

    protected $casts = [
        'published' => 'boolean',
        'looking_for_role' => 'boolean',
    ];

    /**
     * @return HasMany<ResumeSkill, $this>
     */
    public function resumeSkills(): HasMany
    {
        return $this->hasMany(ResumeSkill::class);
    }

    /**
     * @return BelongsToMany<Skill, $this, ResumeSkill>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'resume_skills')
            ->using(ResumeSkill::class)
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Link, $this>
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    /**
     * @return HasMany<WorkExperience, $this>
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ResumeExtraInfo, $this>
     */
    public function extraInfo(): HasMany
    {
        return $this->hasMany(ResumeExtraInfo::class);
    }

    /**
     * @return HasMany<ResumeExtraInfo, $this>
     */
    public function activeExtraInfo(): HasMany
    {
        return $this->extraInfo()->where('is_active', true);
    }
}
