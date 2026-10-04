@php
    $user = $resume->user;
    $fullName = trim("{$user->first_name} {$user->last_name}") ?: $user->name;
    $extraInfos = $resume->activeExtraInfo;
    $links = $resume->links?->sortBy('order');
    $workExperiences = $resume->workExperiences?->sortBy('order');
    $skills = $resume->skills->filter(fn ($skill) => $skill->pivot->is_active);

    $jobTitle = 'Senior Software Engineer';
    $pageTitle = "{$fullName} – {$jobTitle}";
    $introText = strip_tags((string) $resume->intro);
    $description = Str::limit(Str::squish(html_entity_decode($introText, ENT_QUOTES)), 155)
        ?: "Resume of {$fullName}, {$jobTitle}.";
    $canonicalUrl = url('/');
    $currentExperience = $workExperiences->first(fn ($experience) => $experience->in_progress || ! $experience->end_date);
    $personSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $fullName,
        'jobTitle' => $jobTitle,
        'url' => $canonicalUrl,
        'description' => $description,
        'sameAs' => $links->pluck('url')->filter(fn ($url) => Str::startsWith($url, ['http://', 'https://']))->unique()->values()->all(),
        'knowsAbout' => $skills->pluck('name')->values()->all(),
        'worksFor' => $currentExperience ? ['@type' => 'Organization', 'name' => $currentExperience->company_name] : null,
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
    <meta name="twitter:card" content="summary">
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
    <header class="resume-header">
        <div class="resume-identity">
            <div class="resume-heading">
                <h1 class="resume-name">{{ $fullName }}</h1>
                @if($resume->looking_for_role)
                    <span class="status-badge">
                        <span class="status-dot" aria-hidden="true"></span>
                        Open to work
                    </span>
                @endif
            </div>

            @if($extraInfos)
                <p class="extra-info">{{ $extraInfos->pluck('value')->implode(' | ') }}</p>
            @endif

            @if($links->isNotEmpty())
                <ul class="link-list">
                    @foreach($links as $link)
                        <li>
                            <a href="{{ $link->url }}"
                               @if($link->open_in_new_tab) target="_blank" rel="me noopener noreferrer" @else rel="me" @endif>
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                                {{ $link->title ?: $link->url }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <a href="{{route('resume.download')}}" class="download-button">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" x2="12" y1="15" y2="3"/>
            </svg>
            Download CV
        </a>
    </header>

    @if($resume->intro)
        <section class="resume-section">
            <h2 class="section-title">About</h2>
            <div class="rich-text">{!! $resume->intro !!}</div>
        </section>
    @endif

    @if($workExperiences->isNotEmpty())
        <section class="resume-section">
            <h2 class="section-title">Work Experience</h2>

            @foreach($workExperiences as $experience)
                <article class="experience">
                    <header class="experience-header">
                        <div>
                            <h3 class="experience-role">
                                {{ $experience->company_name }} – {{ $experience->role_name }}
                            </h3>
                            @if($experience->location)
                                <span class="experience-location">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ $experience->location }}
                                </span>
                            @endif
                        </div>
                        <p class="experience-dates">
                            <time datetime="{{ $experience->start_date->format('Y-m') }}">{{ $experience->start_date->format('m/Y') }}</time> –
                            @if($experience->in_progress || ! $experience->end_date)
                                Present
                            @else
                                <time datetime="{{ $experience->end_date->format('Y-m') }}">{{ $experience->end_date->format('m/Y') }}</time>
                            @endif
                        </p>
                    </header>
                    <div class="rich-text">{!! $experience->description !!}</div>
                </article>
            @endforeach
        </section>
    @endif

    @if($skills->isNotEmpty())
        <section class="resume-section">
            <h2 class="section-title">Technical Skills</h2>
            <ul class="skill-list">
                @foreach($skills as $skill)
                    <li>{{ $skill->name }}</li>
                @endforeach
            </ul>
        </section>
    @endif
</main>
</body>
</html>
