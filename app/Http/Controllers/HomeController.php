<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Exception;
use Illuminate\Container\Attributes\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class HomeController extends Controller
{
    public function __construct(#[Config('admin.email')] protected ?string $adminEmail)
    {
        if ($this->adminEmail === null) {
            throw new Exception('Admin email is not set');
        }
    }

    /**
     * Show the admin's published resume, or a coming soon page if there is none.
     */
    public function home(): View
    {
        $resume = Resume::publishedFor($this->adminEmail)->with(
            'user',
            'activeExtraInfo',
            'skills',
            'links',
            'workExperiences'
        )
            ->first();

        if ($resume === null) {
            return view('coming-soon');
        }

        return view('home', ['resume' => $resume, 'isPdf' => false]);
    }

    /**
     * Download the admin's published resume as a PDF.
     */
    public function download(): PdfBuilder
    {
        $resume = Resume::publishedFor($this->adminEmail)->with(
            'user',
            'activeExtraInfo',
            'skills',
            'links',
            'workExperiences'
        )
            ->first();

        abort_if($resume === null, 404);
        $user = $resume->user;
        $fileName = Str::slug(trim("{$user->first_name} {$user->last_name}") ?: $user->name, '_').'.pdf';

        return Pdf::view('home', ['resume' => $resume, 'isPdf' => true])
            ->format('a4')
            ->cache()
            ->download($fileName);
    }
}
