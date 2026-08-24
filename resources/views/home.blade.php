hello there!'
'
{{$resume}}
<div class="resume">
    <header>
        <h1>{{ $resume->title }}</h1>
        <div class="intro">{!! $resume->intro !!}</div>
    </header>

    @if($resume->skills->isNotEmpty())
        <section>
            <h2>Skills</h2>
            <ul class="skills">
                @foreach($resume->skills as $skill)
                    <li>{{ $skill->name }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($resume->workExperiences->isNotEmpty())
        <section>
            <h2>Experience</h2>
            @foreach($resume->workExperiences as $experience)
                <article>
                    <h3>{{ $experience->title }} — {{ $experience->location }}</h3>
                    <p class="dates">
                        {{ $experience->start_date->format('M Y') }} –
                        {{ $experience->in_progress ? 'Present' : $experience->end_date->format('M Y') }}
                    </p>
                    <div>{!! $experience->description !!}</div>
                </article>
            @endforeach
        </section>
    @endif

    @if($resume->links->isNotEmpty())
        <section>
            <h2>Links</h2>
            <ul class="links">
                @foreach($resume->links as $link)
                    <li>
                        <a href="{{ $link->url }}"
                           @if($link->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif>
                            {{ $link->title ?? $link->url }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>