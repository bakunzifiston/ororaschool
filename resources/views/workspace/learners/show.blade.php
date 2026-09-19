<x-layouts.platform-workspace :title="$page['person']['name']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-6">
        <x-panel title="Progress on {{ $page['platform']['name'] }}"
                 subtitle="Enrolments on this platform only — other platforms are hidden."
                 :padded="false">
            @if (count($page['enrolments']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['enrolments'] as $enrolment)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-medium text-basalt-900">{{ $enrolment['course'] }}</p>
                                <p class="text-micro text-fern-500">{{ $enrolment['instructor'] }} · {{ $enrolment['lessons_done'] }} lessons done</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-progress-bar :value="(int) $enrolment['progress']" size="sm" class="w-32" />
                                <x-status-badge :status="$enrolment['status']" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="book"
                               title="Not enrolled on this platform yet"
                               message="Put {{ $page['person']['name'] }} on a {{ $page['platform']['name'] }} course and their progress will appear here." />
            @endif
        </x-panel>
    </div>
</x-layouts.platform-workspace>
