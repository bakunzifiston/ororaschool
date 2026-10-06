<x-layouts.learner title="My learning">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        @if ($page['certificatesCount'] > 0)
            <x-slot:actions>
                <a href="{{ route('learner.certificates') }}" class="text-dense font-medium text-accent-700 hover:underline">
                    {{ $page['certificatesCount'] === 1
                        ? '1 certificate earned'
                        : $page['certificatesCount'].' certificates earned' }}
                </a>
            </x-slot:actions>
        @endif
    </x-page-header>

    @if ($page['continue'])
        @php $next = $page['continue']; @endphp
        <section class="mt-8" aria-labelledby="continue-heading">
            <article class="app-card overflow-hidden rounded-md border border-clay-200 bg-chalk">
                <div class="grid gap-0 lg:grid-cols-[minmax(0,16rem)_1fr]">
                    <div class="relative aspect-[16/10] bg-accent-50 lg:aspect-auto lg:min-h-full">
                        @if ($next['course_data']['cover'] ?? null)
                            <img src="{{ asset($next['course_data']['cover']) }}"
                                 alt=""
                                 class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full min-h-36 items-center justify-center">
                                <x-icon name="play" class="h-10 w-10 text-accent-400" />
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col justify-center px-5 py-5 sm:px-6">
                        <p class="text-micro font-medium uppercase tracking-wide text-fern-500">Continue learning</p>
                        <p class="mt-2 text-micro text-fern-500">{{ $next['platform_name'] }} · next lesson</p>
                        <h2 id="continue-heading" class="mt-1 font-display text-section leading-snug text-basalt-900">
                            {{ $next['next'] }}
                        </h2>
                        <p class="mt-1 text-read text-fern-500">
                            From {{ $next['course_data']['title'] }} · due {{ $next['due'] }}
                        </p>

                        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center">
                            <x-progress-bar :value="$next['progress']"
                                            :meta="$next['lessons_done'] . ' of ' . $next['course_data']['lessons'] . ' lessons finished'"
                                            class="max-w-sm" />
                            @if ($next['lesson'] ?? null)
                                <x-button size="lg" icon="play"
                                          :href="route('learner.courses.lessons.show', ['course' => $next['course'], 'lesson' => $next['lesson']['id']])">
                                    Continue learning
                                </x-button>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        </section>
    @endif

    <x-learner-section id="in-progress-heading"
                       title="Courses in progress"
                       href="{{ route('learner.courses', ['status' => 'active']) }}"
                       link="All in-progress courses">
        @if (count($page['inProgress']))
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($page['inProgress'] as $enrolment)
                    <x-course-card :course="$enrolment['course_data']"
                                   :platform-label="$enrolment['platform_name']"
                                   :progress="$enrolment['progress']"
                                   :status="$enrolment['status']"
                                   action="Continue"
                                   action-icon="play"
                                   :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="book" title="No courses in progress"
                           message="When a coordinator enrols you, or you start an open course, the next lesson lands here.">
                <x-slot:actions>
                    <x-button :href="route('learner.courses')">My courses</x-button>
                </x-slot:actions>
            </x-empty-state>
        @endif
    </x-learner-section>

    @if (count($page['upcoming']))
        <x-learner-section title="Upcoming live sessions"
                           href="{{ route('learner.sessions') }}"
                           link="All live sessions">
            <ul class="grid gap-3">
                @foreach ($page['upcoming'] as $session)
                    <li class="flex flex-col gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-medium text-basalt-900">{{ $session['title'] }}</p>
                            <p class="mt-1 text-micro text-fern-500">
                                {{ $session['platform_name'] }}
                                · {{ $session['course'] }}
                                · {{ $session['starts'] }}
                                @if ($session['instructor'] ?? '')
                                    · {{ $session['instructor'] }}
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <x-status-badge :status="$session['status']" />
                            @if ($session['can_join'] ?? false)
                                <form method="POST" action="{{ route('learner.sessions.join', ['session' => $session['id']]) }}">
                                    @csrf
                                    <x-button type="submit" size="sm" icon="video">Join session</x-button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-learner-section>
    @endif

    @if (count($page['paths']))
        <x-learner-section title="Learning paths"
                           href="{{ route('learner.paths') }}"
                           link="All paths">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($page['paths'] as $path)
                    @php
                        $next = null;
                        foreach ($path['items'] as $item) {
                            if (($item['state'] ?? '') !== 'completed') {
                                $next = $item;
                                break;
                            }
                        }
                        $started = (int) ($path['progress'] ?? 0) > 0;
                        $cta = $started ? 'Continue path' : 'Start learning';
                        $courseCount = count($path['items']);
                    @endphp
                    <article class="app-card group flex min-w-0 flex-col overflow-hidden rounded-md border border-clay-200 bg-chalk transition-colors hover:border-clay-300 focus-within:border-accent-500">
                        <div class="relative aspect-[16/9] overflow-hidden bg-accent-50">
                            @if ($path['cover'] ?? null)
                                <img src="{{ asset($path['cover']) }}"
                                     alt=""
                                     width="640"
                                     height="360"
                                     loading="lazy"
                                     decoding="async"
                                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                            @else
                                <span class="flex h-full items-center justify-center">
                                    <x-icon name="path" class="h-7 w-7 text-accent-400" />
                                </span>
                            @endif
                            <span class="absolute right-2 top-2">
                                @if ($path['cross_platform'])
                                    <span class="inline-flex items-center rounded-full border border-basalt-800 bg-basalt-800 px-2 py-0.5 text-micro font-medium text-clay-100">
                                        {{ $path['platform_count'] }} academies
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-clay-200 bg-papyrus px-2 py-0.5 text-micro font-medium text-fern-500">
                                        1 academy
                                    </span>
                                @endif
                            </span>
                        </div>

                        <div class="flex grow flex-col gap-1.5 p-3.5">
                            <h3 class="font-display text-dense font-semibold leading-snug text-basalt-900">
                                <a href="{{ route('learner.paths.show', ['path' => $path['slug']]) }}"
                                   class="rounded-xs hover:text-accent-600">{{ $path['title'] }}</a>
                            </h3>
                            <p class="line-clamp-2 text-micro leading-relaxed text-fern-500">{{ $path['summary'] }}</p>
                            <p class="text-micro text-fern-500">
                                {{ $courseCount }} {{ $courseCount === 1 ? 'course' : 'courses' }}
                                @if ($next)
                                    · Next: {{ $next['course']['title'] }}
                                @endif
                            </p>
                            <div class="mt-auto pt-1.5">
                                <x-progress-bar :value="$path['progress']" size="sm" :meta="implode(' · ', $path['platforms'])" />
                            </div>
                        </div>

                        <div class="border-t border-clay-100 px-3.5 py-2.5">
                            <x-button size="sm"
                                      :icon="$started ? 'play' : null"
                                      :href="route('learner.paths.show', ['path' => $path['slug']])">
                                {{ $cta }}
                            </x-button>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-learner-section>
    @endif

    @if (count($page['available']))
        <x-learner-section title="Available courses"
                           description="Open ones start immediately. The rest ask you to enrol first."
                           href="{{ route('learner.courses') }}"
                           link="My courses">
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

    @if (count($page['certificates']))
        <x-learner-section title="Certificates earned"
                           href="{{ route('learner.certificates') }}"
                           link="All certificates">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($page['certificates'] as $row)
                    <article class="app-card flex min-w-0 flex-col rounded-md border border-clay-200 bg-chalk p-4">
                        <p class="font-mono text-micro text-fern-500">{{ $row['code'] }}</p>
                        <h3 class="mt-1 font-display text-panel font-semibold leading-snug text-basalt-900">{{ $row['course'] }}</h3>
                        <p class="mt-1 text-micro text-fern-500">{{ $row['platform_name'] }} · issued {{ $row['issued'] }}</p>
                        <div class="mt-3">
                            <x-button size="sm" :href="route('certificates.verify', ['code' => $row['code']])">View certificate</x-button>
                        </div>
                    </article>
                @endforeach
            </div>
        </x-learner-section>
    @endif

    @if (array_sum($page['charts']['mix']['values']) > 0)
        <x-learner-section title="Learning mix">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <x-panel>
                    <x-pie-chart :title="$page['charts']['mix']['title']"
                                 :subtitle="$page['charts']['mix']['subtitle']"
                                 :headline="$page['charts']['mix']['headline'] ?? null"
                                 :labels="$page['charts']['mix']['labels']"
                                 :values="$page['charts']['mix']['values']" />
                </x-panel>
                <x-panel>
                    <x-bar-chart orientation="horizontal"
                                 :title="$page['charts']['progress']['title']"
                                 :subtitle="$page['charts']['progress']['subtitle']"
                                 :labels="$page['charts']['progress']['labels']"
                                 :values="$page['charts']['progress']['values']"
                                 :suffix="$page['charts']['progress']['suffix'] ?? ''"
                                 :max="$page['charts']['progress']['max'] ?? null" />
                </x-panel>
            </div>
        </x-learner-section>
    @endif
</x-layouts.learner>
