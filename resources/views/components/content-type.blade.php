@props(['type' => 'text'])

@php
    $icon = \App\Support\DemoData\Pages\CurriculumPage::typeIcon($type);
    $label = \App\Support\DemoData\Pages\CurriculumPage::types()[$type] ?? $type;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-sm border border-clay-200 bg-papyrus px-1.5 py-0.5 text-micro text-fern-500']) }}>
    <x-icon :name="$icon" class="h-3 w-3" />
    {{ $label }}
</span>
