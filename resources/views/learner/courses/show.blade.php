<x-layouts.learner :title="$page['syllabus']['course']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['cta'] === 'enroll')
                <form method="POST" action="{{ route('learner.courses.enroll', ['course' => $page['syllabus']['course']['slug']]) }}">
                    @csrf
                    <x-button type="submit">Enrol</x-button>
                </form>
            @elseif ($page['cta'] === 'start' && $page['syllabus']['current'])
                <x-button icon="play"
                          :href="route('learner.courses.lessons.show', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $page['syllabus']['current']['id']])">
                    Start
                </x-button>
            @elseif ($page['cta'] === 'continue' && $page['syllabus']['current'])
                <x-button icon="play"
                          :href="route('learner.courses.lessons.show', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $page['syllabus']['current']['id']])">
                    Continue
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($page['cta'] === 'enroll')
        <p class="mt-4 rounded-md border border-clay-200 bg-papyrus px-4 py-3 text-dense text-fern-600">
            Enrolment is required for this course. There is no Start button until you are on the roster.
        </p>
    @elseif ($page['cta'] === 'start')
        <p class="mt-4 rounded-md border border-clay-200 bg-papyrus px-4 py-3 text-dense text-fern-600">
            Enrolment is not required. Start opens the first lesson on your record.
        </p>
    @endif

    @if ($page['syllabus']['enrolled'])
        <div class="mt-6">
            <x-progress-bar :value="$page['syllabus']['progress']"
                            label="Your progress"
                            :meta="$page['syllabus']['platform']['name']" />
        </div>
    @endif

    <ol class="mt-6 grid gap-4">
        @foreach ($page['syllabus']['modules'] as $module)
            <li class="rounded-md border border-clay-200 bg-chalk">
                <h2 class="border-b border-clay-200 bg-papyrus px-4 py-2.5 font-display text-panel font-semibold text-basalt-900">
                    {{ $module['title'] }}
                </h2>
                <ol class="divide-y divide-clay-100">
                    @foreach ($module['lessons'] as $lesson)
                        <li class="flex flex-wrap items-center gap-3 px-4 py-2.5">
                            @if ($lesson['state'] === 'locked')
                                <span class="flex min-w-0 grow items-center gap-2 text-dense text-fern-400">
                                    <x-icon name="lock" class="h-3.5 w-3.5" />
                                    {{ $lesson['title'] }}
                                </span>
                            @else
                                <a href="{{ route('learner.courses.lessons.show', ['course' => $page['syllabus']['course']['slug'], 'lesson' => $lesson['id']]) }}"
                                   class="min-w-0 grow font-medium text-basalt-900 hover:text-accent-600">{{ $lesson['title'] }}</a>
                            @endif
                            <x-content-type :type="$lesson['type']" />
                            @if ($lesson['state'] === 'completed')
                                <x-status-badge status="completed" />
                            @elseif ($lesson['state'] === 'in_progress')
                                <x-status-badge status="active">In progress</x-status-badge>
                            @else
                                <span class="text-micro text-fern-400">Locked</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </li>
        @endforeach
    </ol>
</x-layouts.learner>
