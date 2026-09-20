@props([
    'courses' => [],
    'pagination' => [],
    'emptyTitle' => '',
    'emptyMessage' => '',
    'clearHref' => null,
    'detail' => false,
])

@if (count($courses))
    <div>
        @foreach ($courses as $course)
            <x-public.course-row :course="$course" :detail="$detail" />
        @endforeach
    </div>

    @if (($pagination['pages'] ?? 1) > 1)
        <x-pagination :pagination="$pagination" class="mt-8" />
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
