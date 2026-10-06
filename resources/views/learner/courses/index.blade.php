<x-layouts.learner title="My courses">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    @if ($page['showFilters'])
        <form method="GET" action="{{ route('learner.courses') }}" class="mt-6 flex flex-wrap items-end gap-3">
            <div class="w-52">
                <x-select name="status" label="Show" size="sm" :autosubmit="true"
                          :options="$page['filters']['statuses']"
                          :selected="$page['filters']['status']" />
            </div>
            <div class="w-52">
                <x-select name="platform" label="Academy" size="sm" :autosubmit="true"
                          :options="$page['filters']['platforms']"
                          :selected="$page['filters']['platform']" />
            </div>
            <div class="w-52">
                <x-select name="category" label="Category" size="sm" :autosubmit="true"
                          :options="$page['filters']['categories']"
                          :selected="$page['filters']['category']" />
            </div>
            @if (filled($page['filters']['status']) || filled($page['filters']['platform']) || filled($page['filters']['category']))
                <a href="{{ route('learner.courses') }}" class="mb-1 text-dense font-medium text-accent-700 hover:underline">Clear filters</a>
            @endif
        </form>
    @endif

    @if (count($page['sections']))
        @foreach ($page['sections'] as $section)
            <x-learner-section :title="$section['title']">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($section['courses'] as $enrolment)
                        @php
                            $action = ($enrolment['status'] ?? '') === 'completed'
                                ? 'View course'
                                : ((int) ($enrolment['progress'] ?? 0) > 0 ? 'Continue' : 'Start learning');
                        @endphp
                        <x-course-card :course="$enrolment['course_data']"
                                       :platform-label="$enrolment['platform_name']"
                                       :progress="$enrolment['progress']"
                                       :status="$enrolment['status']"
                                       :action="$action"
                                       :action-icon="$action === 'View course' ? null : 'play'"
                                       :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
                    @endforeach
                </div>
            </x-learner-section>
        @endforeach
    @elseif (! count($page['available']))
        <div class="mt-8">
            <x-panel :padded="false">
                <x-empty-state icon="book"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        </div>
    @endif

    @if (count($page['available']))
        <x-learner-section title="{{ count($page['sections']) ? 'Also on the catalogue' : 'Available courses' }}"
                           description="{{ count($page['sections']) ? 'Open ones start immediately. The rest ask you to enrol first.' : null }}">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($page['available'] as $item)
                    <x-course-card variant="public"
                                   size="sm"
                                   :course="$item['course']"
                                   :platform-label="$item['platform_name']"
                                   action="View course"
                                   :href="route('learner.courses.show', ['course' => $item['course']['slug']])" />
                @endforeach
            </div>
        </x-learner-section>
    @endif
</x-layouts.learner>
