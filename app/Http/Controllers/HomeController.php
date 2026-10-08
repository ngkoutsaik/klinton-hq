<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Exception;
use Illuminate\Container\Attributes\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class HomeController extends Controller
{
    public function __construct(#[Config('admin.email')] protected ?string $adminEmail) {}

    /**
     * Show the admin's published resume, or 404 if there is none.
     */
    public function home(): View
    {
        $resume = $this->getResume();

        abort_if($resume === null, 404);

        return view('home', ['resume' => $resume, 'isPdf' => false]);
    }

    /**
     * Download the admin's published resume as a PDF.
     */
    public function download(): PdfBuilder
    {
        $resume = $this->getResume();

        abort_if($resume === null, 404);
        $user = $resume->user;
        $fileName = Str::slug(trim("{$user->first_name} {$user->last_name}") ?: $user->name, '_').'.pdf';

        return Pdf::view('home', ['resume' => $resume, 'isPdf' => true])
            ->format('a4')
            ->cache()
            ->download($fileName);
    }

    /**
     * List the public pages for search engines.
     */
    public function sitemap(): Response
    {
        $resume = $this->getResume();

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

    /**
     * Load the admin's published resume with required relationships.
     */
    protected function getResume(): ?Resume
    {
        if ($this->adminEmail === null) {
            throw new Exception('Admin email is not set');
        }

        return Resume::where('published', true)
            ->whereRelation('user', 'email', $this->adminEmail)
            ->with(
                'user',
                'activeExtraInfo',
                'skills',
                'links',
                'workExperiences'
            )->first();
    }
}
