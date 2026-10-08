<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Exception;
use Illuminate\Container\Attributes\Config;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function __construct(#[Config('admin.email')] protected ?string $adminEmail) {}

    /**
     * List the public pages for search engines.
     */
    public function sitemap(): Response
    {
        if ($this->adminEmail === null) {
            throw new Exception('Admin email is not set');
        }

        $resume = Resume::publishedFor($this->adminEmail)->first();

        abort_if($resume === null, 404);

        return response()
            ->view('sitemap', ['resume' => $resume])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Allow all crawlers and point them to the sitemap.
     */
    public function robots(): Response
    {
        return response("User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n")
            ->header('Content-Type', 'text/plain');
    }
}
