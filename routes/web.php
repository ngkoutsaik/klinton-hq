<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\NoIndex;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', [HomeController::class, 'home']);

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots']);

Route::get('/download', [HomeController::class, 'download'])->name('resume.download')
    ->middleware(['throttle:20,1', NoIndex::class]);

Route::post('/contact', [ContactFormController::class, 'store'])->name('contact.store')
    ->middleware(['throttle:5,1', ProtectAgainstSpam::class]);
