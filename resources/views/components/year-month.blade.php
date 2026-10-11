@props(['year', 'month' => null])

@if($month)
    <time datetime="{{ sprintf('%d-%02d', $year, $month) }}">{{ sprintf('%02d/%d', $month, $year) }}</time>
@else
    <time datetime="{{ $year }}">{{ $year }}</time>
@endif
