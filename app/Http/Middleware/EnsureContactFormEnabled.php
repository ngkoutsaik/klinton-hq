<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class EnsureContactFormEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Config::boolean('contact.enabled')) {
            App::abort(404);
        }

        return $next($request);
    }
}
