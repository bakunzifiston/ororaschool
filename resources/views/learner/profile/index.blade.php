<x-layouts.learner title="Profile">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <form method="POST" action="{{ route('learner.profile.update') }}" class="lg:col-span-2">
            @csrf
            <x-panel title="Your details">
                <div class="grid gap-5">
                    <x-field name="name" label="Name" size="sm" :value="$page['user']['name']" required />
                    <x-field name="email" type="email" label="Email" size="sm" :value="$page['user']['email']" required />
                    <x-field name="district" label="District" size="sm" :value="$page['user']['district']" />
                    <x-field name="phone" label="Phone" size="sm" :value="$page['user']['phone'] ?? ''" />
                    <x-button type="submit">Save</x-button>
                </div>
            </x-panel>
        </form>

        <div class="lg:col-span-3">
            <x-panel title="Learning history" subtitle="One timeline. Every platform you have learned on." :padded="false">
                <ol class="divide-y divide-clay-100">
                    @foreach ($page['history'] as $entry)
                        <li class="px-4 py-3">
                            <p class="text-micro text-fern-400">{{ $entry['at'] }} · {{ $entry['platform'] }}</p>
                            <p class="mt-0.5 font-medium text-basalt-900">{{ $entry['title'] }}</p>
                            <p class="mt-0.5 text-dense text-fern-500">{{ $entry['detail'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-panel>

            <p class="mt-3 text-micro text-fern-400">
                Platforms on this record:
                {{ collect($page['platforms'])->pluck('name')->join(', ', ' and ') }}.
            </p>
        </div>
    </div>
</x-layouts.learner>
