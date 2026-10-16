<?php

namespace App\Http\Responders;

use App\Http\Controllers\ContactFormController;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Spatie\Honeypot\SpamResponder\SpamResponder;

class ContactFormSpamResponder implements SpamResponder
{
    /**
     * Answer spam with the same response a genuine submission gets, so a bot
     * learns nothing about why it was rejected. The request never reaches the
     * controller, so nothing is stored and no mail is dispatched.
     */
    public function respond(Request $request, Closure $next): RedirectResponse
    {
        return Redirect::to(URL::to('/').'#contact')
            ->with('contact_status', ContactFormController::SUCCESS_MESSAGE);
    }
}
