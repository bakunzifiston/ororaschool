<x-layouts.learner title="Live sessions">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    @if (! count($page['upcoming']) && ! count($page['past']))
        <div class="mt-8">
            <x-panel :padded="false">
                <x-empty-state icon="video"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('learner.courses')">My courses</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        </div>
    @else
        <x-learner-section title="Upcoming sessions">
            @if (count($page['upcoming']))
                <ul class="grid gap-4">
                    @foreach ($page['upcoming'] as $session)
                        <li class="app-card flex flex-col gap-4 rounded-md border border-clay-200 bg-chalk p-5 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-display text-panel font-semibold text-basalt-900">{{ $session['title'] }}</h3>
                                    <x-status-badge :status="$session['status']" />
                                </div>
                                <p class="mt-2 text-dense text-fern-500">
                                    {{ $session['date'] }}
                                    @if ($session['time'] !== '')
                                        · {{ $session['time'] }}
                                    @endif
                                    · {{ $session['duration'] }} min
                                </p>
                                <p class="mt-1 text-micro text-fern-500">
                                    {{ $session['platform_name'] }} · {{ $session['course'] }}
                                    @if ($session['instructor'] ?? '')
                                        · {{ $session['instructor'] }}
                                    @endif
                                </p>
                            </div>
                            @if ($session['can_join'] ?? false)
                                <form method="POST" action="{{ route('learner.sessions.join', ['session' => $session['id']]) }}" class="shrink-0">
                                    @csrf
                                    <x-button type="submit" icon="video">Join session</x-button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state class="py-8" icon="video"
                               title="Nothing scheduled on your courses."
                               message="When a live session is scheduled against a course you are on, it appears here." />
            @endif
        </x-learner-section>

        <x-learner-section title="Past sessions">
            @if (count($page['past']))
                <ul class="grid gap-3">
                    @foreach ($page['past'] as $session)
                        <li class="flex flex-col gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-medium text-basalt-900">{{ $session['title'] }}</p>
                                <p class="mt-1 text-micro text-fern-500">
                                    {{ $session['platform_name'] }}
                                    · {{ $session['date'] }}
                                    @if ($session['instructor'] ?? '')
                                        · {{ $session['instructor'] }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <x-status-badge :status="$session['status']" />
                                @if ($session['recording'] ?? '')
                                    <a href="{{ $session['recording'] }}" class="text-micro font-medium text-accent-700 hover:underline">Recording</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-dense text-fern-500">No past clinics on your record yet.</p>
            @endif
        </x-learner-section>
    @endif
</x-layouts.learner>
