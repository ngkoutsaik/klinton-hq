<?php

namespace App\Models;

use Database\Factories\UserLinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    /** @use HasFactory<UserLinkFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
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
