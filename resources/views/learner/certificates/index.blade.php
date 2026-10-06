<x-layouts.learner title="Certificates">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @forelse ($page['rows'] as $row)
            <article class="app-card flex min-w-0 flex-col overflow-hidden rounded-md border border-clay-200 bg-chalk">
                <div class="flex h-32 items-center justify-center bg-accent-50">
                    <x-icon name="award" class="h-10 w-10 text-accent-400" />
                </div>
                <div class="flex grow flex-col gap-2 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-mono text-micro text-fern-500">{{ $row['code'] }}</p>
                        <x-status-badge :status="$row['status']" />
                    </div>
                    <h2 class="font-display text-panel font-semibold leading-snug text-basalt-900">{{ $row['course'] }}</h2>
                    <p class="text-micro text-fern-500">{{ $row['platform_name'] }} · issued {{ $row['issued'] }}</p>
                    <div class="mt-3">
                        <x-button size="sm" :href="route('certificates.verify', ['code' => $row['code']])">View certificate</x-button>
                    </div>
                </div>
            </article>
        @empty
            <x-panel class="sm:col-span-2" :padded="false">
                <x-empty-state icon="award"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('learner.courses')">My courses</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @endforelse
    </div>
</x-layouts.learner>
