<x-layouts.public :title="$page['course']['title']" flush>
    @php
        $course = $page['course'];
        $lessons = (int) ($course['lessons'] ?? 0);
        $instructors = implode(', ', $course['instructors'] ?? [$course['instructor']]);
        $meta = collect([
            $course['platform_name'] ?? null,
            $course['academy'] ?? null,
            ($course['paid'] ?? false) ? 'Paid' : 'Free',
            $lessons > 0 ? $lessons.' '.($lessons === 1 ? 'lesson' : 'lessons') : null,
        ])->filter()->implode(' · ');
    @endphp

    <x-public.hero-band compact :image="$course['cover'] ?? null" :alt="$course['title']">
        <x-public.trail on-dark :items="[
            ['label' => 'Courses', 'route' => 'catalog.courses'],
            ['label' => $course['platform_name'], 'route' => 'catalog.platforms.show', 'params' => ['platform' => $course['platform']]],
            ['label' => $course['title']],
        ]" />

        <p class="mt-6 flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
            <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
            FARMSCHOOL
        </p>

        <h1 class="mt-5 max-w-3xl marketing-hero text-chalk">{{ $course['title'] }}</h1>

        <p class="mt-4 max-w-xl text-read leading-relaxed text-clay-200">{{ $course['description'] }}</p>

        @if ($meta !== '')
            <p class="mt-5 text-dense text-clay-300">{{ $meta }}</p>
        @endif
    </x-public.hero-band>

    <x-public.section tone="white" class="grow">
        <div class="grid gap-10 lg:grid-cols-5 lg:items-start lg:gap-12">
            <div class="lg:col-span-3">
                <dl class="grid grid-cols-2 gap-x-6 gap-y-5 border-b border-clay-200 pb-8 text-dense sm:grid-cols-3">
                    <div class="flex gap-3">
                        <x-icon name="teacher" class="mt-0.5 h-4 w-4 text-fern-500" />
                        <div>
                            <dt class="text-micro text-fern-500">Instructor</dt>
                            <dd class="mt-1 text-basalt-900">{{ $instructors }}</dd>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <x-icon name="clock" class="mt-0.5 h-4 w-4 text-fern-500" />
                        <div>
                            <dt class="text-micro text-fern-500">Duration</dt>
                            <dd class="mt-1 text-basalt-900">
                                <x-public.duration :minutes="$course['duration'] ?? 0" />
                            </dd>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <x-icon name="globe" class="mt-0.5 h-4 w-4 text-fern-500" />
                        <div>
                            <dt class="text-micro text-fern-500">Language</dt>
                            <dd class="mt-1 text-basalt-900">{{ $course['language'] }}</dd>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <x-icon name="gauge" class="mt-0.5 h-4 w-4 text-fern-500" />
                        <div>
                            <dt class="text-micro text-fern-500">Difficulty</dt>
                            <dd class="mt-1 text-basalt-900">{{ $course['difficulty'] }}</dd>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <x-icon name="tag" class="mt-0.5 h-4 w-4 text-fern-500" />
                        <div>
                            <dt class="text-micro text-fern-500">Price</dt>
                            <dd class="mt-1 text-basalt-900">{{ ($course['paid'] ?? false) ? 'Paid' : 'Free' }}</dd>
                        </div>
                    </div>
                    @if ($course['certificate_eligible'] ?? false)
                        <div class="flex gap-3">
                            <x-icon name="award" class="mt-0.5 h-4 w-4 text-fern-500" />
                            <div>
                                <dt class="text-micro text-fern-500">Certificate</dt>
                                <dd class="mt-1 text-basalt-900">Certificate eligible</dd>
                            </div>
                        </div>
                    @endif
                </dl>

                <section class="mt-10">
                    <h2 class="font-display text-section text-basalt-900">What you will work through</h2>
                    <p class="mt-2 max-w-xl text-read text-fern-600">Module and lesson titles only. The material itself opens after you sign in.</p>

                    <div class="mt-6 divide-y divide-clay-200 border-y border-clay-200">
                        @foreach ($page['syllabus'] as $module)
                            <details class="group" @if ($loop->first) open @endif>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-4 text-left marker:content-none [&::-webkit-details-marker]:hidden">
                                    <span class="font-medium text-basalt-900">{{ $module['title'] }}</span>
                                    <span class="flex shrink-0 items-center gap-3 text-micro text-fern-500">
                                        {{ count($module['lessons']) }} {{ count($module['lessons']) === 1 ? 'lesson' : 'lessons' }}
                                        <x-icon name="chevron-down" class="h-4 w-4 text-fern-500 transition-transform group-open:rotate-180" />
                                    </span>
                                </summary>
                                <ul class="list-disc space-y-2 border-t border-clay-100 pb-4 pl-5 pt-3">
                                    @foreach ($module['lessons'] as $lesson)
                                        <li class="text-dense text-basalt-800 marker:text-fern-400">
                                            <span class="flex flex-wrap items-baseline justify-between gap-3">
                                                <span>{{ $lesson['title'] }}</span>
                                                @if ($lesson['is_preview'])
                                                    <span class="text-micro font-medium text-accent-700">Preview available</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="lg:col-span-2 lg:sticky lg:top-24">
                <div class="rounded-md border border-basalt-800 bg-basalt-900 p-5 text-chalk sm:p-6">
                    <p class="font-display text-section text-chalk">
                        {{ ($course['enrollment_required'] ?? true) ? 'Enrolment is required' : 'Open to start' }}
                    </p>
                    <p class="mt-3 text-dense leading-relaxed text-clay-200">
                        Sign in to {{ ($course['enrollment_required'] ?? true) ? 'join the roster' : 'open the first lesson' }}.
                        A visitor who is not signed in cannot start a lesson from this page.
                    </p>
                    <div class="mt-6">
                        <x-button :href="route('login')" class="w-full">{{ $page['cta'] }}</x-button>
                    </div>
                </div>
            </div>
        </div>
    </x-public.section>
</x-layouts.public>
