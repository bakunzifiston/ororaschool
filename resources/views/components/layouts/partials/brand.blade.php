@props(['href' => '/', 'onBasalt' => false, 'stacked' => false])

@php
    $name = config('app.name');
    $classes = $stacked
        ? 'inline-flex flex-col items-start'
        : 'inline-flex items-center gap-2 py-1.5';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($stacked)
        <img src="{{ asset('images/brand/farmschool-lockup.png') }}"
             alt="{{ $name }}"
             class="h-28 w-auto sm:h-32">
    @else
        <img src="{{ asset('images/brand/farmschool-mark.png') }}"
             alt=""
             class="h-8 w-auto">
        <span class="font-display text-dense font-semibold tracking-tight">
            <span @class(['text-chalk' => $onBasalt, 'text-basalt-900' => ! $onBasalt])>Farm</span><span class="text-accent-500">school</span>
        </span>
    @endif
</a>
