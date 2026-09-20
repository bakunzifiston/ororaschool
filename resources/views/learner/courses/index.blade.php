<x-layouts.learner title="My courses">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('learner.courses', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('learner.courses') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-52">
            <x-select name="status" label="Show" size="sm" :autosubmit="true"
                      :options="$page['filters']['statuses']"
                      :selected="$page['filters']['status']" />
        </div>
    </form>

    <div class="mt-6 grid gap-8">
        @forelse ($page['groups'] as $group)
            <section>
                <header class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
                    <div>
                        <h2 class="font-display text-section text-basalt-900">{{ $group['name'] }}</h2>
                        <p class="text-micro text-fern-500">{{ $group['discipline'] }}</p>
                    </div>
                    <span class="figure text-micro text-fern-500">{{ count($group['courses']) }} {{ count($group['courses']) === 1 ? 'course' : 'courses' }}</span>
                </header>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($group['courses'] as $enrolment)
                        <x-course-card :course="$enrolment['course_data']"
                                       :platform-label="$enrolment['platform_name']"
                                       :progress="$enrolment['progress']"
                                       :status="$enrolment['status']"
                                       :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
                    @endforeach
                </div>
            </section>
        @empty
            <x-panel :padded="false">
                <x-empty-state icon="book"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @endforelse
    </div>

    @if (count($page['available']))
        <section class="mt-10">
            <h2 class="mb-1 font-display text-section text-basalt-900">Also on the catalogue</h2>
            <p class="mb-4 text-micro text-fern-500">Open ones start immediately. The rest ask you to enrol first.</p>

            <ul class="grid gap-3">
                @foreach ($page['available'] as $item)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('learner.courses.show', ['course' => $item['course']['slug']]) }}"
                               class="font-medium text-basalt-900 hover:text-accent-600">{{ $item['course']['title'] }}</a>
                            <p class="text-micro text-fern-500">{{ $item['platform_name'] }}</p>
                        </div>
                        @if ($item['course']['enrollment_required'])
                            <x-button :href="route('learner.courses.show', ['course' => $item['course']['slug']])">Enrol</x-button>
                        @else
                            <x-button variant="secondary" :href="route('learner.courses.show', ['course' => $item['course']['slug']])">Start</x-button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.learner>
