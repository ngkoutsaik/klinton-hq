<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactFormRequest;
use App\Models\ContactForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class ContactFormController extends Controller
{
    public const string SUCCESS_MESSAGE = 'Thanks, your message has been received.';

    public function store(StoreContactFormRequest $request): RedirectResponse
    {
        ContactForm::create([
            ...$request->safe()->only(['email', 'subject', 'message']),
            'send_copy' => $request->boolean('send_copy'),
        ]);

        return Redirect::to(URL::to('/').'#contact')
            ->with('contact_status', self::SUCCESS_MESSAGE);
    }
}
