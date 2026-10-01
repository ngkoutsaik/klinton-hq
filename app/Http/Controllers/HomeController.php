<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class HomeController extends Controller
{
    /**
     * Show the admin's published resume, or redirect to 404 if there is none.
     */
    public function home(): View|RedirectResponse
    {
        $resume = $this->getResume();

        if ($resume === null) {
            return redirect('/404');
        }

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
            ->download($fileName);
    }

    /**
     * Load the admin's published resume with required relationships.
     */
    protected function getResume(): ?Resume
    {
        $superAdminId = 1;

        return Resume::where(['user_id' => $superAdminId, 'published' => true])->with(
            'user',
            'activeExtraInfo',
            'skills',
            'links',
            'workExperiences'
        )->first();
    }
}
