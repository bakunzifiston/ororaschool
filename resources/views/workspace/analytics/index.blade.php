<x-layouts.platform-workspace title="Analytics" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.analytics', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($page['stats'] as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']"
                         :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @forelse ($page['charts'] as $chart)
            <x-panel>
                <x-bar-chart :title="$chart['title']"
                             :subtitle="$chart['subtitle']"
                             :labels="$chart['labels']"
                             :values="$chart['values']"
                             :suffix="$chart['suffix'] ?? ''" />
            </x-panel>
        @empty
            <x-panel class="lg:col-span-2" :padded="false">
                <x-empty-state icon="chart" title="No analytics on this platform yet"
                               message="Enrolments, completions and quiz scores fill these charts once learners start." />
            </x-panel>
        @endforelse
    </div>

    <div class="mt-6">
        <x-panel title="Top courses by enrolment" subtitle="This platform only."
                 variant="table" :padded="false">
            <x-data-table :columns="$page['topCourses']['columns']"
                          :rows="$page['topCourses']['rows']"
                          empty-title="No courses to rank yet"
                          empty-message="Publish a course and this ranking will fill in."
                          min-width="40rem" />
        </x-panel>
    </div>
</x-layouts.platform-workspace>
