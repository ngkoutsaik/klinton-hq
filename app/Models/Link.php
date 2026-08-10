<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'target',
        'icon',
        'open_in_new_tab',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
    ];
}
