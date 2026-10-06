<x-layouts.public title="Explore courses" flush>
    <x-public.section tone="forest" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Courses'],
        ]" />

        <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
            <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
            FARMSCHOOL
        </p>

        <x-public.page-heading
            class="mt-6"
            on-dark
            :title="$page['title']"
            :subtitle="$page['subtitle']" />
    </x-public.section>

    <x-public.section tone="white" class="grow">
        <x-public.filters :filters="$page['filters']"
                          :options="$page['options']"
                          :show-platform-filter="$page['showPlatformFilter']"
                          :show-academy-filter="filled($page['filters']['platform'] ?? '') || filled($page['filters']['academy'] ?? '')"
                          :form-action="$page['formAction']" />

        <div class="mt-10">
            @if (count($page['courses']))
                <p class="text-dense text-fern-500">
                    {{ number_format($page['pagination']['total'] ?? count($page['courses'])) }}
                    {{ ($page['pagination']['total'] ?? count($page['courses'])) === 1 ? 'course' : 'courses' }}
                </p>

                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
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
