<?php

namespace App\Models;

use App\Enums\LinkIcon;
use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'icon',
        'open_in_new_tab',
        'resume_id',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'icon' => LinkIcon::class,
    ];
}
