@props([
    'filters' => [],
    'options' => [],
    'showPlatformFilter' => true,
    'formAction' => '',
    'posted' => false,
])

<form method="GET" action="{{ $formAction }}"
      @class([
          'grid gap-3 sm:grid-cols-2 lg:grid-cols-3' => ! $posted,
          'border-y border-clay-200 py-5' => $posted,
      ])>
    <div @class(['contents' => ! $posted, 'grid gap-4 sm:grid-cols-2 lg:grid-cols-3' => $posted])>
        <x-field name="q" label="Search" size="sm"
                 :value="$filters['q'] ?? ''"
                 placeholder="Title or description" />

        @if ($showPlatformFilter)
            <x-select name="platform" label="Platform" size="sm"
                      :options="$options['platforms'] ?? []"
                      :selected="$filters['platform'] ?? ''" />
        @endif

        <x-select name="academy" label="Academy" size="sm"
                  :options="$options['academies'] ?? []"
                  :selected="$filters['academy'] ?? ''" />

        <x-select name="difficulty" label="Difficulty" size="sm"
                  :options="$options['difficulties'] ?? []"
                  :selected="$filters['difficulty'] ?? ''" />

        <x-select name="language" label="Language" size="sm"
                  :options="$options['languages'] ?? []"
                  :selected="$filters['language'] ?? ''" />

        <x-select name="price" label="Price" size="sm"
                  :options="$options['prices'] ?? []"
                  :selected="$filters['price'] ?? ''" />

        <div class="flex items-end gap-2">
            @if ($posted)
                <x-button type="submit" variant="secondary">Apply filters</x-button>
                <x-button variant="ghost" :href="$formAction">Clear</x-button>
            @else
                <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
                <x-button variant="ghost" size="sm" :href="$formAction">Clear</x-button>
            @endif
        </div>
    </div>
</form>
