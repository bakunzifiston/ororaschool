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
        $courseCount = (int) ($platform['public_courses'] ?? 0);
        $foci = array_values(array_filter(array_column($platform['public_academies'] ?? [], 'name')));
    @endphp

    <x-public.hero-band compact :image="$platform['cover'] ?? null" :alt="$platform['name']">
        <x-public.trail on-dark :items="[
            ['label' => 'Academies', 'route' => 'catalog.platforms'],
            ['label' => $platform['name']],
        ]" />

        <p class="inline-flex items-center gap-1.5 rounded-full bg-chalk/15 px-2.5 py-1 text-micro font-medium text-chalk">
            <x-icon :name="$glyph" class="h-3.5 w-3.5" />
            {{ $platform['discipline'] }}
        </p>
        <h1 class="mt-4 marketing-title text-chalk">{{ $platform['name'] }}</h1>
        <p class="mt-3 max-w-xl text-read leading-relaxed text-clay-200">
            {{ $platform['tagline'] ?: $platform['description'] }}
        </p>
    </x-public.hero-band>

    <x-public.section tone="chalk" class="grow">
        <dl class="grid gap-6 border-b border-clay-200 pb-8 sm:grid-cols-3 sm:gap-8">
            @if (($platform['region'] ?? '') !== '')
                <div>
                    <dt class="text-micro text-fern-500">Region</dt>
                    <dd class="mt-1 text-dense text-basalt-900">{{ $platform['region'] }}</dd>
                </div>
            @endif
            <div>
                <dt class="text-micro text-fern-500">Published courses</dt>
                <dd class="mt-1 text-dense text-basalt-900">
                    {{ $courseCount }} {{ $courseCount === 1 ? 'course' : 'courses' }}
                </dd>
            </div>
            @if (count($foci))
                <div>
                    <dt class="text-micro text-fern-500">Focus</dt>
                    <dd class="mt-1 text-dense text-basalt-900">{{ implode(', ', $foci) }}</dd>
                </div>
            @endif
        </dl>

        @if (count($page['resources'] ?? []))
            <div class="mt-10 border-b border-clay-200 pb-10">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-public.section-heading
                        title="Resources"
                        subtitle="Open academy handouts. Course, module and lesson files stay with enrolment." />
                    <a href="{{ route('catalog.resources', ['platform' => $platform['slug']]) }}"
                       class="text-dense font-medium text-accent-700 hover:underline">
                        All resources
                    </a>
                </div>

                <ul class="mt-6 divide-y divide-clay-100">
                    @foreach ($page['resources'] as $resource)
                        <li class="flex flex-wrap items-baseline justify-between gap-3 py-3">
                            <div>
                                <p class="font-medium text-basalt-900">{{ $resource['title'] }}</p>
                                <p class="text-micro text-fern-500">{{ $resource['type_label'] }} · {{ $resource['attached_to'] }}</p>
                            </div>
                            <p class="text-micro text-fern-500">{{ $resource['size'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-10">
            <x-public.section-heading title="Courses" />

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
        </div>
    </x-public.section>
</x-layouts.public>
