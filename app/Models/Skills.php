<?php

namespace App\Models;

use Database\Factories\SkillsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    /** @use HasFactory<SkillsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
