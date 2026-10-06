<x-layouts.public :title="$page['platform']['name']" flush>
    @php
        $platform = $page['platform'];
        $courseCount = (int) ($platform['public_courses'] ?? count($page['courses'] ?? []));
        $meta = collect([
            $platform['discipline'] ?? null,
            $platform['region'] ?? null,
            $courseCount > 0
                ? $courseCount.' '.($courseCount === 1 ? 'course' : 'courses')
                : null,
        ])->filter()->implode(' · ');
    @endphp

    <x-public.hero-band compact :image="$platform['cover'] ?? null" :alt="$platform['name']">
        <x-public.trail on-dark :items="[
            ['label' => 'Academies', 'route' => 'catalog.platforms'],
            ['label' => $platform['name']],
        ]" />

        <p class="mt-6 flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
            <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
            FARMSCHOOL
        </p>

        <h1 class="mt-5 marketing-hero text-chalk">{{ $platform['name'] }}</h1>

        <p class="mt-4 max-w-xl text-read leading-relaxed text-clay-200">
            {{ $platform['tagline'] ?: $platform['description'] }}
        </p>

        @if ($meta !== '')
            <p class="mt-5 text-dense text-clay-300">{{ $meta }}</p>
        @endif
    </x-public.hero-band>

    <x-public.section tone="white" class="grow">
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
                        <x-public.course-tile :course="$course" :omit-platform="true" />
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

        @if (count($page['resources'] ?? []))
            <div class="mt-16 border-t border-clay-200 pt-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-public.section-heading
                        title="Resources"
                        subtitle="Open academy handouts. Course, module and lesson files stay with enrolment." />
                    <a href="{{ route('catalog.resources', ['platform' => $platform['slug']]) }}"
                       class="text-dense font-medium text-accent-700 hover:underline">
                        All resources
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($page['resources'] as $resource)
                        <x-public.resource-card :resource="$resource" :omit-platform="true" />
                    @endforeach
                </div>
            </div>
        @endif
    </x-public.section>
</x-layouts.public>
