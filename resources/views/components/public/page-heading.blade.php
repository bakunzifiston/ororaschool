@props(['title', 'subtitle' => null, 'onDark' => false])

<div {{ $attributes->merge(['class' => 'max-w-2xl']) }}>
    <h1 @class(['marketing-title', 'text-chalk' => $onDark, 'text-basalt-900' => ! $onDark])>{{ $title }}</h1>
    @if (filled($subtitle))
        <p @class([
            'mt-3 text-read leading-relaxed',
            'text-clay-200' => $onDark,
            'text-fern-600' => ! $onDark,
        ])>{{ $subtitle }}</p>
    @endif
</div>
