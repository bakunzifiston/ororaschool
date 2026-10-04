@props(['resource' => []])

@php
    $type = (string) ($resource['type'] ?? '');
    $tones = [
        'manual' => ['band' => 'bg-st-approved-bg', 'ink' => 'text-st-approved', 'icon' => 'book'],
        'guide' => ['band' => 'bg-ok-bg', 'ink' => 'text-ok', 'icon' => 'book'],
        'template' => ['band' => 'bg-st-active-bg', 'ink' => 'text-st-active', 'icon' => 'file'],
        'document' => ['band' => 'bg-papyrus', 'ink' => 'text-fern-600', 'icon' => 'file'],
        'infographic' => ['band' => 'bg-st-pending-bg', 'ink' => 'text-st-pending', 'icon' => 'layers'],
        'presentation' => ['band' => 'bg-st-completed-bg', 'ink' => 'text-st-completed', 'icon' => 'chart'],
        'pdf' => ['band' => 'bg-accent-50', 'ink' => 'text-accent-600', 'icon' => 'file'],
        'video' => ['band' => 'bg-danger-bg', 'ink' => 'text-danger', 'icon' => 'video'],
    ];
    $tone = $tones[$type] ?? ['band' => 'bg-accent-50', 'ink' => 'text-accent-600', 'icon' => 'file'];
    $title = $resource['title'] ?? '';
    $href = $resource['href'] ?? '#';
    $typeLabel = $resource['type_label'] ?? $type;
    $platformName = $resource['platform_name'] ?? '';
    $attachedTo = $resource['attached_to'] ?? '';
    $size = $resource['size'] ?? '';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'public-card public-card-hover group flex h-full min-w-0 items-start gap-4 p-5']) }}>
    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-md {{ $tone['band'] }}">
        <x-icon :name="$tone['icon']" class="h-5 w-5 {{ $tone['ink'] }}" />
    </span>

    <span class="flex min-w-0 grow flex-col">
        <span class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-micro font-medium {{ $tone['band'] }} {{ $tone['ink'] }}">
                {{ $typeLabel }}
            </span>
            @if ($platformName !== '')
                <span class="text-micro font-medium text-fern-500">{{ $platformName }}</span>
            @endif
        </span>

        <span class="mt-1.5 font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
            {{ $title }}
        </span>

        @if ($attachedTo !== '')
            <span class="mt-1.5 text-dense leading-relaxed text-fern-600">{{ $attachedTo }}</span>
        @endif

        <span class="mt-auto flex items-center justify-between gap-3 pt-4">
            <span class="text-micro text-fern-500">{{ $size }}</span>

            <span class="inline-flex items-center gap-1 text-dense font-medium text-accent-700">
                Open
                <x-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5" />
            </span>
        </span>
    </span>
</a>
