<?php

namespace App\Models;

use Database\Factories\ContactFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    /** @use HasFactory<ContactFormFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'subject',
        'message',
    ];

    protected $attributes = [
        'sent' => false,
    ];

    protected $casts = [
        'sent' => 'boolean',
    ];
}
