@props([
    'courses' => [],
    'pagination' => [],
    'emptyTitle' => '',
    'emptyMessage' => '',
    'clearHref' => null,
])

@if (count($courses))
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach ($courses as $course)
            <x-course-card variant="public"
                           :course="$course"
                           :platform-label="$course['platform_name']"
                           :href="$course['href']" />
        @endforeach
    </div>

    @if (($pagination['pages'] ?? 1) > 1)
        <x-pagination :pagination="$pagination" class="mt-8 rounded-md border border-clay-200 bg-chalk" />
    @endif
@else
    <x-empty-state icon="search" :title="$emptyTitle" :message="$emptyMessage">
        <x-slot:actions>
            @if ($clearHref)
                <x-button variant="secondary" :href="$clearHref">Clear filters</x-button>
            @endif
        </x-slot:actions>
    </x-empty-state>
@endif
