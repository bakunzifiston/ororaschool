@props(['minutes' => 0])

@php
    $minutes = (int) $minutes;
    $hours = intdiv($minutes, 60);
    $rest = $minutes % 60;
    $label = $hours
        ? trim($hours.'h'.($rest ? ' '.$rest.'m' : ''))
        : $rest.'m';
@endphp

<span {{ $attributes->merge(['class' => 'figure']) }}>{{ $label }}</span>
