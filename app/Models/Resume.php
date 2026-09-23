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

    protected $appends = [
        'userContactInfo'
    ];

    /**
     * @return HasMany<ResumeSkill, $this>
     */
    public function resumeSkills(): HasMany
    {
        return $this->hasMany(ResumeSkill::class);
    }

    /**
     * @return BelongsToMany<Skill, $this>
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
     * @return BelongsTo<BelongsTo,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extraInfo(): HasMany
    {
        return $this->hasMany(ResumeExtraInfo::class);
    }

    /**
     * @throws \Exception
     */
    public function getUserContactInfoAttribute(): array
    {
        $user = $this->user()->first();
        if (!$user) {
            throw new \Exception('User not found');
        }

        return [
            'email' => $user->get('email'),
//            'phone' => $user->get('phone'),
            'name' => $user->get('name'),
//            'last_name' => $user->get('last_name'),
        ];
    }
}
