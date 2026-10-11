<section class="resume-section">
    <h2 class="section-title">{{$title}}</h2>

    @foreach($entries as $entry)
        <article class="entry">
            <header class="entry-header">
                <div>
                    <h3 class="entry-role">
                        {{ $entry->organization }} – {{ $entry->title }}
                    </h3>
                    @if($entry->location)
                        <span class="entry-location">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ $entry->location }}
                                </span>
                    @endif
                </div>
                <p class="entry-dates">
                    <x-year-month :year="$entry->start_year" :month="$entry->start_month"/>
                    –
                    @if($entry->in_progress || ! $entry->end_year)
                        Present
                    @else
                        <x-year-month :year="$entry->end_year" :month="$entry->end_month"/>
                    @endif
                </p>
            </header>
            @if($entry->description)
                <div class="rich-text">{!! $entry->description !!}</div>
            @endif
        </article>
    @endforeach
</section>
