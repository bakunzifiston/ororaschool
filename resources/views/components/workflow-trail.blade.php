@props([
    'steps' => ['draft', 'pending_review', 'approved', 'published', 'archived'],
    'current' => 'draft',
])

@php
    $labels = [
        'draft' => 'Draft',
        'pending_review' => 'Pending review',
        'approved' => 'Approved',
        'published' => 'Published',
        'archived' => 'Archived',
    ];
    $currentIndex = array_search($current, $steps, true);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp

<ol {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-0']) }} aria-label="Publishing workflow">
    @foreach ($steps as $index => $step)
        @php
            $state = $index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'todo');
        @endphp
        <li class="flex items-center">
            @if ($index > 0)
                <span @class([
                    'mx-1 h-px w-6 sm:w-10',
                    'bg-basalt-800' => $state !== 'todo',
                    'bg-clay-200' => $state === 'todo',
                ]) aria-hidden="true"></span>
            @endif
            <span @class([
                'inline-flex items-center gap-1.5 rounded-sm px-2 py-1 text-micro font-medium',
                'border border-basalt-800 bg-basalt-800 text-clay-100' => $state === 'current',
                'border border-clay-200 bg-papyrus text-fern-500' => $state === 'done',
                'border border-dashed border-clay-300 bg-transparent text-fern-400' => $state === 'todo',
            ])>
                @if ($state === 'done')
                    <x-icon name="check" class="h-3 w-3" />
                @else
                    <span class="figure">{{ $index + 1 }}</span>
                @endif
                {{ $labels[$step] ?? $step }}
            </span>
        </li>
    @endforeach
</ol>
