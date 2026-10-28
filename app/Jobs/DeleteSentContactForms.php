<?php

namespace App\Jobs;

use App\Models\ContactForm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Date;

class DeleteSentContactForms implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        ContactForm::query()
            ->where('sent', true)
            ->where('updated_at', '<=', Date::now()->subDays(7))
            ->delete();
    }
}
