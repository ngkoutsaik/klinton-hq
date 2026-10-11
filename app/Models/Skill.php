<?php

namespace App\Models;

use App\Enums\SkillType;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'type' => SkillType::class,
    ];
}
