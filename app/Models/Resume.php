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
}
