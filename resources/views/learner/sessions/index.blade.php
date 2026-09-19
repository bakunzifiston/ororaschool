<x-layouts.learner title="Live sessions">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('learner.sessions', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    @if (! count($page['upcoming']) && ! count($page['past']))
        <div class="mt-6">
            <x-panel :padded="false">
                <x-empty-state icon="video"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        </div>
    @else
        <section class="mt-6">
            <h2 class="mb-3 font-display text-section text-basalt-900">Upcoming</h2>
            <ul class="grid gap-3">
                @forelse ($page['upcoming'] as $session)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-medium text-basalt-900">{{ $session['title'] }}</p>
                            <p class="text-micro text-fern-500">{{ $session['platform_name'] }} · {{ $session['course'] }} · {{ $session['starts'] }}</p>
                        </div>
                        <form method="POST" action="{{ route('learner.sessions.join', ['session' => $session['id']]) }}">
                            @csrf
                            <x-button type="submit" size="sm">Join</x-button>
                        </form>
                    </li>
                @empty
                    <li class="text-dense text-fern-500">Nothing scheduled on your courses.</li>
                @endforelse
            </ul>
        </section>

        <section class="mt-8">
            <h2 class="mb-3 font-display text-section text-basalt-900">Past sessions</h2>
            <ul class="grid gap-3">
                @forelse ($page['past'] as $session)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-medium text-basalt-900">{{ $session['title'] }}</p>
                            <p class="text-micro text-fern-500">{{ $session['platform_name'] }} · {{ $session['starts'] }}</p>
                        </div>
                        @if ($session['recording'] ?? '')
                            <a href="{{ $session['recording'] }}" class="text-micro font-medium text-accent-700 hover:underline">Recording</a>
                        @else
                            <x-status-badge :status="$session['status']" />
                        @endif
                    </li>
                @empty
                    <li class="text-dense text-fern-500">No past clinics on your record yet.</li>
                @endforelse
            </ul>
        </section>
    @endif
</x-layouts.learner>
