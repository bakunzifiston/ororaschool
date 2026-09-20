<x-layouts.public :title="$page['platform']['name']">
    @php
        $platform = $page['platform'];
        $glyphs = [
            'ororafarm' => 'sprout',
            'gemura' => 'droplet',
            'buchapro' => 'tag',
            'feedgrid' => 'layers',
        ];
        $glyph = $glyphs[$platform['slug'] ?? ''] ?? 'book';
    @endphp

    <x-public.trail :items="[
        ['label' => 'Platforms', 'route' => 'catalog.platforms'],
        ['label' => $platform['name']],
    ]" />

    <header class="max-w-2xl">
        <p class="inline-flex items-center gap-1.5 rounded-md bg-accent-50 px-2.5 py-1 text-micro font-medium text-accent-700">
            <x-icon :name="$glyph" class="h-3.5 w-3.5" />
            {{ $platform['discipline'] }}
        </p>
        <h1 class="mt-4 font-display text-title text-basalt-900">{{ $platform['name'] }}</h1>
        @if (($platform['region'] ?? '') !== '')
            <p class="mt-2 text-dense text-fern-500">{{ $platform['region'] }}</p>
        @endif
        <p class="mt-4 text-read leading-relaxed text-fern-600">{{ $platform['description'] }}</p>
    </header>

    @if (count($platform['public_academies']))
        <section class="mt-10">
            <h2 class="font-display text-section text-basalt-900">Academies</h2>
            <ul class="mt-4 flex flex-wrap gap-2">
                @foreach ($platform['public_academies'] as $academy)
                    <li class="rounded-md border border-clay-200 bg-chalk px-3 py-1.5 text-dense text-basalt-800">
                        {{ $academy['name'] }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="mt-12">
        <h2 class="font-display text-section text-basalt-900">Published courses</h2>
        <p class="mt-2 max-w-xl text-read text-fern-600">Published {{ $platform['name'] }} courses from the same catalogue.</p>

        <div class="public-card mt-6 p-5 sm:p-6">
            <x-public.filters :filters="$page['filters']"
                              :options="$page['options']"
                              :show-platform-filter="$page['showPlatformFilter']"
                              :form-action="$page['formAction']" />
        </div>

        <div class="mt-8">
            @if (count($page['courses']))
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($page['courses'] as $course)
                        <x-public.course-tile :course="$course" />
                    @endforeach
                </div>

                @if (($page['pagination']['pages'] ?? 1) > 1)
                    <x-pagination :pagination="$page['pagination']" class="mt-8" />
                @endif
            @else
                <x-empty-state icon="search" :title="$page['emptyTitle']" :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button variant="secondary" :href="$page['formAction']">Clear filters</x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </div>
    </section>
</x-layouts.public>
