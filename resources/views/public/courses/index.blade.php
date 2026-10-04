<x-layouts.public title="Explore courses" flush>
    <x-public.section tone="terrace" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Courses'],
        ]" />

        <x-public.page-heading
            on-dark
            title="Explore courses"
            subtitle="Practical training from across the four live academies." />
    </x-public.section>

    <x-public.section tone="chalk" class="grow">
        <x-public.filters :filters="$page['filters']"
                          :options="$page['options']"
                          :show-platform-filter="$page['showPlatformFilter']"
                          :form-action="$page['formAction']" />

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
