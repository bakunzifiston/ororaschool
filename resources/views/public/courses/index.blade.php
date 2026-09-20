<x-layouts.public title="Explore courses">
    <x-public.trail :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Courses'],
    ]" />

    <h1 class="font-display text-title text-basalt-900">Explore courses</h1>
    <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">
        Practical training from across the four live platforms.
    </p>

    <div class="public-card mt-8 p-5 sm:p-6">
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
</x-layouts.public>
