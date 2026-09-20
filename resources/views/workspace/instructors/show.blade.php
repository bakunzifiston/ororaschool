<x-layouts.platform-workspace :title="$page['person']['name']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-panel title="On this platform">
            <x-role-chip :role="$page['role']" show-scope />
            <p class="mt-3 text-micro text-fern-500">{{ $page['person']['email'] }}</p>
            <p class="text-micro text-fern-500">Last seen {{ $page['person']['last_seen'] }}</p>
        </x-panel>

        <x-panel class="lg:col-span-2" title="Assigned courses" :padded="false">
            @if (count($page['courses']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['courses'] as $course)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <a href="{{ route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $course['slug']]) }}"
                               class="font-medium text-basalt-900 hover:text-accent-600">{{ $course['title'] }}</a>
                            <x-status-badge :status="$course['status']" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="book" title="No courses assigned yet"
                               message="Attach {{ $page['person']['name'] }} as the instructor of a course and it will appear here." />
            @endif
        </x-panel>
    </div>
</x-layouts.platform-workspace>
