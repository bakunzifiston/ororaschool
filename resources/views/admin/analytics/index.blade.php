<x-layouts.super-admin title="Analytics">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($page['stats'] as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']"
                         :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @foreach ($page['charts'] as $chart)
            <x-panel>
                <x-bar-chart :title="$chart['title']"
                             :subtitle="$chart['subtitle']"
                             :labels="$chart['labels']"
                             :values="$chart['values']"
                             :suffix="$chart['suffix'] ?? ''" />
            </x-panel>
        @endforeach
    </div>

    <div class="mt-6">
        <x-panel title="Platform comparison" subtitle="Same figures as the estate list, side by side."
                 variant="table" :padded="false">
            <x-data-table :columns="$page['comparison']['columns']"
                          :rows="$page['comparison']['rows']"
                          min-width="40rem" />
        </x-panel>
    </div>
</x-layouts.super-admin>
