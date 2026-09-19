<x-layouts.learner title="Certificates">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('learner.certificates', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @forelse ($page['rows'] as $row)
            <article class="flex min-w-0 flex-col rounded-md border border-clay-200 bg-chalk">
                <div class="flex h-28 items-center justify-center rounded-t-md border-b border-clay-200 bg-accent-50">
                    <x-icon name="award" class="h-8 w-8 text-accent-400" />
                </div>
                <div class="flex grow flex-col gap-2 p-4">
                    <p class="font-mono text-micro text-fern-500">{{ $row['code'] }}</p>
                    <h2 class="font-display text-panel font-semibold leading-snug text-basalt-900">{{ $row['course'] }}</h2>
                    <p class="text-micro text-fern-500">{{ $row['platform_name'] }} · issued {{ $row['issued'] }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$row['status']" />
                        <a href="{{ route('certificates.verify', ['code' => $row['code']]) }}"
                           class="text-micro font-medium text-accent-700 hover:underline">View / Share</a>
                    </div>
                </div>
            </article>
        @empty
            <x-panel class="sm:col-span-2" :padded="false">
                <x-empty-state icon="award"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @endforelse
    </div>
</x-layouts.learner>
