<x-layouts.public :title="$page['course']['title']">
    @php
        $course = $page['course'];
        $glyphs = [
            'ororafarm' => 'sprout',
            'gemura' => 'droplet',
            'buchapro' => 'tag',
            'feedgrid' => 'layers',
        ];
        $glyph = $glyphs[$course['platform'] ?? ''] ?? 'book';
        $lessons = (int) ($course['lessons'] ?? 0);
    @endphp

    <x-public.trail :items="[
        ['label' => 'Courses', 'route' => 'catalog.courses'],
        ['label' => $course['platform_name'], 'route' => 'catalog.platforms.show', 'params' => ['platform' => $course['platform']]],
        ['label' => $course['title']],
    ]" />

    <div class="grid gap-8 lg:grid-cols-5 lg:items-start lg:gap-10">
        <header class="lg:col-span-3">
            <p class="inline-flex items-center gap-1.5 rounded-md bg-accent-50 px-2.5 py-1 text-micro font-medium text-accent-700">
                <x-icon :name="$glyph" class="h-3.5 w-3.5" />
                {{ $course['platform_name'] }}
            </p>
            <h1 class="mt-4 font-display text-title text-basalt-900">{{ $course['title'] }}</h1>
            <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">{{ $course['description'] }}</p>
        </header>

        <div class="lg:col-span-2 lg:row-span-2 lg:sticky lg:top-20">
            <div class="public-card on-basalt bg-basalt-900 p-5 text-chalk sm:p-6">
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

        <dl class="public-card grid grid-cols-2 gap-x-6 gap-y-4 p-5 text-dense lg:col-span-3">
            <div>
                <dt class="text-micro text-fern-500">Instructor</dt>
                <dd class="mt-1 text-basalt-900">{{ implode(', ', $course['instructors'] ?? [$course['instructor']]) }}</dd>
            </div>
            <div>
                <dt class="text-micro text-fern-500">Duration</dt>
                <dd class="mt-1 text-basalt-900">
                    <x-public.duration :minutes="$course['duration'] ?? 0" />
                </dd>
            </div>
            <div>
                <dt class="text-micro text-fern-500">Language</dt>
                <dd class="mt-1 text-basalt-900">{{ $course['language'] }}</dd>
            </div>
            <div>
                <dt class="text-micro text-fern-500">Difficulty</dt>
                <dd class="mt-1 text-basalt-900">{{ $course['difficulty'] }}</dd>
            </div>
            <div>
                <dt class="text-micro text-fern-500">Price</dt>
                <dd class="mt-1 text-basalt-900">{{ ($course['paid'] ?? false) ? 'Paid' : 'Free' }}</dd>
            </div>
            @if ($lessons > 0)
                <div>
                    <dt class="text-micro text-fern-500">Lessons</dt>
                    <dd class="mt-1 text-basalt-900">{{ $lessons }}</dd>
                </div>
            @endif
            @if ($course['certificate_eligible'] ?? false)
                <div>
                    <dt class="text-micro text-fern-500">Certificate</dt>
                    <dd class="mt-1 text-basalt-900">Certificate eligible</dd>
                </div>
            @endif
        </dl>
    </div>

    <section class="mt-12">
        <h2 class="font-display text-section text-basalt-900">What you will work through</h2>
        <p class="mt-2 max-w-xl text-read text-fern-600">Module and lesson titles only. The material itself opens after you sign in.</p>

        <ol class="public-card mt-6 divide-y divide-clay-100 overflow-hidden">
            @foreach ($page['syllabus'] as $module)
                <li class="px-5 py-5 sm:px-6">
                    <h3 class="font-medium text-basalt-900">{{ $module['title'] }}</h3>
                    <ol class="mt-3 grid gap-2">
                        @foreach ($module['lessons'] as $lesson)
                            <li class="flex flex-wrap items-baseline justify-between gap-3 text-dense text-basalt-800">
                                <span>{{ $lesson['title'] }}</span>
                                @if ($lesson['is_preview'])
                                    <span class="rounded-sm bg-accent-50 px-2 py-0.5 text-micro font-medium text-accent-700">Preview available</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </li>
            @endforeach
        </ol>
    </section>
</x-layouts.public>
