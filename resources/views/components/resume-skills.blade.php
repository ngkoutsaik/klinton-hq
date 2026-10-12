@use('App\Enums\SkillType')
@props(['skillsByType'])
<section class="resume-section">
    <h2 class="section-title">Skills</h2>

    @foreach(SkillType::cases() as $skillType)
        @if($skillsByType->has($skillType->value))
            <div class="skill-group">
                <h3 class="skill-group-title">{{ $skillType->getLabel() }}</h3>
                <ul class="skill-list">
                    @foreach($skillsByType->get($skillType->value) as $skill)
                        <li>{{ $skill->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</section>
