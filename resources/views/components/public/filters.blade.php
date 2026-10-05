@props([
    'filters' => [],
    'options' => [],
    'showPlatformFilter' => true,
    'showAcademyFilter' => true,
    'formAction' => '',
])

@php
    $selects = [];

    if ($showPlatformFilter) {
        $selects[] = ['name' => 'platform', 'label' => 'Academy', 'options' => $options['platforms'] ?? []];
    }

    if ($showAcademyFilter) {
        $selects[] = ['name' => 'academy', 'label' => 'Focus', 'options' => $options['academies'] ?? []];
    }
    $selects[] = ['name' => 'difficulty', 'label' => 'Difficulty', 'options' => $options['difficulties'] ?? []];
    $selects[] = ['name' => 'language', 'label' => 'Language', 'options' => $options['languages'] ?? []];
    $selects[] = ['name' => 'price', 'label' => 'Price', 'options' => $options['prices'] ?? []];

    $hasActiveFilters = filled($filters['q'] ?? '')
        || collect($selects)->contains(fn (array $select) => filled($filters[$select['name']] ?? ''));
@endphp

<form method="GET" action="{{ $formAction }}" class="public-filters">
    <div class="flex flex-wrap items-center gap-2">
        <div class="relative min-w-48 flex-1">
            <label for="public-filter-q" class="sr-only">Search</label>
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-fern-500" />
            <input id="public-filter-q"
                   type="search"
                   name="q"
                   value="{{ $filters['q'] ?? '' }}"
                   placeholder="Search courses"
                   class="h-11 w-full rounded-full border border-clay-200 bg-chalk py-0 pr-4 pl-10 text-dense text-basalt-800 placeholder:text-fern-500">
        </div>

        <x-button type="submit" class="shrink-0">Search</x-button>

        @foreach ($selects as $select)
            @php
                $value = $filters[$select['name']] ?? '';
                $active = filled($value);
            @endphp

            <label for="public-filter-{{ $select['name'] }}" class="sr-only">{{ $select['label'] }}</label>
            <select id="public-filter-{{ $select['name'] }}"
                    name="{{ $select['name'] }}"
                    onchange="this.form.submit()"
                    @class([
                        'h-11 w-44 shrink-0 rounded-full border px-3.5 text-dense',
                        'border-accent-200 bg-accent-50 text-accent-700' => $active,
                        'border-clay-200 bg-chalk text-basalt-800' => ! $active,
                    ])>
                @foreach ($select['options'] as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $optionValue === (string) $value)>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @endforeach

        @if ($hasActiveFilters)
            <a href="{{ $formAction }}" class="inline-flex h-11 shrink-0 items-center px-2 text-dense font-medium text-accent-700 hover:underline">
                Clear
            </a>
        @endif
    </div>
</form>
