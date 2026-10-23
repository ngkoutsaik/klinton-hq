@use('Illuminate\Support\Facades\Config')
@php
    $user = $resume->user;
    $fullName = trim("{$user->first_name} {$user->last_name}") ?: $user->name;
    $extraInfos = $resume->activeExtraInfo;
    $links = $resume->links?->sortBy('order');
    $workExperiences = $resume->workExperiences?->sortBy('order');
    $education = $resume->education?->sortBy('order');
    $skills = $resume->activeSkills;
    $skillsByType = $skills->groupBy(fn ($skill) => $skill->type->value);
    $showContactForm = Config::boolean('contact.enabled') && $isPdf !== true;

    $jobTitle = 'Senior Software Engineer';
    $pageTitle = "{$fullName} – {$jobTitle}";
    $introText = strip_tags((string) $resume->intro);
    $description = Str::limit(Str::squish(html_entity_decode($introText, ENT_QUOTES)), 155)
        ?: "Resume of {$fullName}, {$jobTitle}.";
    $canonicalUrl = url('/');
    $currentExperience = $workExperiences->first(fn ($experience) => $experience->in_progress || ! $experience->end_year);
    $personSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $fullName,
        'jobTitle' => $jobTitle,
        'url' => $canonicalUrl,
        'description' => $description,
        'sameAs' => $links->pluck('url')->filter(fn ($url) => Str::startsWith($url, ['http://', 'https://']))->unique()->values()->all(),
        'knowsAbout' => $skills->pluck('name')->values()->all(),
        'worksFor' => $currentExperience ? ['@type' => 'Organization', 'name' => $currentExperience->organization] : null,
    ]);
@endphp
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($user->first_name && $user->last_name)
        <meta property="profile:first_name" content="{{ $user->first_name }}">
        <meta property="profile:last_name" content="{{ $user->last_name }}">
    @endif
    <meta property="og:image" content="{{ URL::asset('social-preview.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $description }}">

    <script type="application/ld+json">{!! json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @if($isPdf===true)
        <style>{!! Vite::content('resources/css/resume.css') !!}</style>
    @else
        @vite('resources/css/resume.css')
    @endif
</head>
<body>
<main class="resume">
    <x-resume-header
        :full-name="$fullName"
        :looking-for-role="$resume->looking_for_role"
        :extra-infos="$extraInfos"
        :links="$links"
        :is-pdf="$isPdf"
        :show-contact-form="$showContactForm"
    ></x-resume-header>

    @if($resume->intro)
        <section class="resume-section">
            <h2 class="section-title">About</h2>
            <div class="rich-text">{!! $resume->intro !!}</div>
        </section>
    @endif

    @if($workExperiences->isNotEmpty())
        <x-resume-entries title='Work Experience' :entries="$workExperiences"></x-resume-entries>
    @endif

    @if($education->isNotEmpty())
        <x-resume-entries title="Education" :entries="$education"></x-resume-entries>
    @endif
    @if($skillsByType->isNotEmpty())
        <x-resume-skills :skills-by-type="$skillsByType"></x-resume-skills>
    @endif

    @if($showContactForm)
        <x-contact-form></x-contact-form>
    @endif
</main>
</body>
</html>
