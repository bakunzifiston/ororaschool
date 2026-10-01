<x-layouts.public :title="$page['platform']['name']" flush>
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

    <x-public.hero-band :image="$platform['cover'] ?? null" :alt="$platform['name']">
        <x-public.trail on-dark :items="[
            ['label' => 'Platforms', 'route' => 'catalog.platforms'],
            ['label' => $platform['name']],
        ]" />

        <p class="inline-flex items-center gap-1.5 rounded-full bg-chalk/15 px-2.5 py-1 text-micro font-medium text-chalk">
            <x-icon :name="$glyph" class="h-3.5 w-3.5" />
            {{ $platform['discipline'] }}
        </p>
        <h1 class="mt-4 marketing-title text-chalk">{{ $platform['name'] }}</h1>
        @if (($platform['region'] ?? '') !== '')
            <p class="mt-2 text-dense text-clay-200">{{ $platform['region'] }}</p>
        @endif
        <p class="mt-4 max-w-2xl text-read leading-relaxed text-clay-200">{{ $platform['description'] }}</p>

        @if (count($platform['public_academies']))
            <ul class="mt-6 flex flex-wrap gap-2">
                @foreach ($platform['public_academies'] as $academy)
                    <li class="rounded-full bg-chalk/15 px-3 py-1.5 text-dense text-chalk">
                        {{ $academy['name'] }}
                    </li>
                @endforeach
            </ul>
        @endif
    </x-public.hero-band>

    <x-public.section tone="chalk" class="grow">
        <x-public.section-heading
            title="Published courses"
            :subtitle="'Published '.$platform['name'].' courses from the same catalogue.'" />

        <div class="mt-8">
            <x-public.filters :filters="$page['filters']"
                              :options="$page['options']"
                              :show-platform-filter="$page['showPlatformFilter']"
                              :form-action="$page['formAction']" />
        </div>

        <div class="mt-10">
            @if (count($page['courses']))
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($page['courses'] as $course)
                        <x-public.course-tile :course="$course" />
                    @endforeach
                </div>

                @if (($page['pagination']['pages'] ?? 1) > 1)
                    <x-pagination :pagination="$page['pagination']" class="mt-10 px-0" />
                @endif
            @else
                <x-empty-state icon="search" :title="$page['emptyTitle']" :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button variant="secondary" :href="$page['formAction']">Clear filters</x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </div>
    </x-public.section>
</x-layouts.public>
