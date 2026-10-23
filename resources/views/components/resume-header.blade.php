@props(['fullName', 'lookingForRole', 'extraInfos', 'links', 'isPdf', 'showContactForm'])
<header class="resume-header">
    <div class="resume-identity">
        <div class="resume-heading">
            <h1 class="resume-name">{{ $fullName }}</h1>
            @if($lookingForRole)
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
                           @if($link->open_in_new_tab) target="_blank" rel="me noopener noreferrer"
                           @else rel="me" @endif>
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                            @if($isPdf===true)
                                {{  $link->url }}
                            @else
                                {{ $link->title ?: $link->url }}
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="header-actions">
        @if($showContactForm)
            <a href="#contact" class="download-button">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
                Contact
            </a>
        @endif
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
    </div>
</header>
