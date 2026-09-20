<x-layouts.learner :title="$page['lesson']['title']" width="read">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-6">
        @php $type = $page['lesson']['type']; @endphp

        @if ($type === 'video')
            <div class="flex aspect-video items-center justify-center rounded-md border border-clay-200 bg-basalt-900 text-clay-100">
                <span class="flex flex-col items-center gap-2">
                    <x-icon name="play" class="h-10 w-10 text-accent-400" />
                    <span class="text-micro text-fern-500">Video placeholder · {{ $page['lesson']['duration'] }} min</span>
                </span>
            </div>
            <p class="mt-4 text-read leading-relaxed text-basalt-800">{{ $page['lesson']['body'] }}</p>
        @elseif ($type === 'text')
            <article class="rounded-md border border-clay-200 bg-chalk px-5 py-5">
                <p class="text-read leading-relaxed text-basalt-800">{{ $page['lesson']['body'] }}</p>
            </article>
        @elseif ($type === 'pdf')
            <div class="flex min-h-64 flex-col items-center justify-center gap-3 rounded-md border border-dashed border-clay-300 bg-papyrus px-4 py-10">
                <x-icon name="file" class="h-8 w-8 text-fern-500" />
                <p class="font-medium text-basalt-900">PDF viewer placeholder</p>
                <p class="max-w-sm text-center text-micro text-fern-500">{{ $page['lesson']['title'] }} would open here. Download is a placeholder in this build.</p>
            </div>
        @elseif ($type === 'audio')
            <div class="rounded-md border border-clay-200 bg-chalk px-5 py-6">
                <p class="text-micro text-fern-500">Audio player placeholder</p>
                <p class="mt-1 font-display text-panel font-semibold text-basalt-900">{{ $page['lesson']['title'] }}</p>
                <div class="mt-4 flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-accent-500 text-accent-on">
                        <x-icon name="play" class="h-4 w-4" />
                    </span>
                    <span class="h-1.5 grow rounded-full bg-clay-200">
                        <span class="block h-full w-1/4 rounded-full bg-accent-500"></span>
                    </span>
                    <span class="figure text-micro text-fern-500">{{ $page['lesson']['duration'] }}:00</span>
                </div>
                <p class="mt-4 text-read leading-relaxed text-fern-600">{{ $page['lesson']['body'] }}</p>
            </div>
        @elseif ($type === 'external')
            <div class="rounded-md border border-clay-200 bg-chalk px-5 py-6">
                <p class="text-micro text-fern-500">External resource</p>
                <p class="mt-1 text-read leading-relaxed text-basalt-800">{{ $page['lesson']['body'] }}</p>
                <div class="mt-4">
                    <x-button variant="secondary" icon="link" href="https://www.minagri.gov.rw/" target="_blank" rel="noreferrer">
                        Open the note
                    </x-button>
                </div>
            </div>
        @elseif ($type === 'live_session')
            <div class="rounded-md border border-clay-200 bg-chalk px-5 py-6">
                <p class="text-micro text-fern-500">Live session join placeholder</p>
                <p class="mt-1 font-display text-panel font-semibold text-basalt-900">{{ $page['lesson']['title'] }}</p>
                @if ($page['session'] ?? null)
                    <p class="mt-1 text-dense text-fern-500">{{ $page['session']['instructor'] }} · {{ $page['session']['starts'] }}</p>
                    <p class="mt-4 text-dense text-fern-600">{{ $page['lesson']['body'] }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('learner.sessions.join', ['session' => $page['session']['id']]) }}">
                            @csrf
                            <x-button type="submit" icon="video">Join session</x-button>
                        </form>
                        @if ($page['session']['recording'] ?? '')
                            <x-button variant="secondary" icon="play" :href="$page['session']['recording']">Watch the recording</x-button>
                        @endif
                    </div>
                @else
                    <p class="mt-4 text-dense text-fern-600">{{ $page['lesson']['body'] }}</p>
                    <p class="mt-2 text-micro text-fern-500">The join link will appear here when a clinic is scheduled.</p>
                @endif
            </div>
        @endif
    </div>

    @if ($page['lesson']['quiz'] ?? null)
        <p class="mt-6 text-dense">
            <a href="{{ route('learner.quizzes.show', ['quiz' => $page['lesson']['quiz']]) }}"
               class="font-medium text-accent-700 hover:underline">Take the quiz for this lesson</a>
        </p>
    @endif

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-clay-200 pt-4">
        @if ($page['prev'] ?? null)
            <x-button variant="secondary" icon="chevron-left"
                      :href="route('learner.courses.lessons.show', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $page['prev']['id']])">
                Previous
            </x-button>
        @else
            <span></span>
        @endif

        <form method="POST" action="{{ route('learner.courses.lessons.complete', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $page['lesson']['id']]) }}">
            @csrf
            <x-button type="submit" variant="secondary">Mark complete</x-button>
        </form>

        @if ($page['next'] ?? null)
            <x-button icon-after="chevron-right"
                      :href="route('learner.courses.lessons.show', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $page['next']['id']])">
                Next
            </x-button>
        @else
            <x-button variant="ghost" :href="route('learner.courses.show', ['course' => $page['syllabus']['course']['slug']])">Back to syllabus</x-button>
        @endif
    </div>
</x-layouts.learner>
