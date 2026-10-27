<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactFormRequest;
use App\Models\ContactForm;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ContactFormController extends Controller implements HasMiddleware
{
    public const string SUCCESS_MESSAGE = 'Thanks, your message has been received.';

    /**
     * @return array<Closure>
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next): Response {
                abort_unless(Config::boolean('contact.enabled'), 404);

                return $next($request);
            },
        ];
    }

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
