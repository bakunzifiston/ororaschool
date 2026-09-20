<x-layouts.learner :title="$page['path']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['path']['cross_platform'])
                <span class="rounded-sm border border-basalt-800 bg-basalt-800 px-2 py-0.5 text-micro font-medium text-clay-100">
                    {{ $page['path']['platform_count'] }} platforms
                </span>
            @else
                <span class="rounded-sm border border-clay-200 bg-papyrus px-2 py-0.5 text-micro text-fern-500">
                    1 platform
                </span>
            @endif
        </x-slot:actions>
    </x-page-header>

    <p class="mt-4 text-dense text-fern-600">{{ $page['path']['summary'] }}</p>

    <div class="mt-6">
        <x-progress-bar :value="$page['path']['progress']" label="Path progress" />
    </div>

    <ol class="mt-6 grid gap-3">
        @foreach ($page['path']['items'] as $index => $item)
            <li class="flex flex-wrap items-center gap-4 rounded-md border border-clay-200 bg-chalk px-4 py-3">
                <span class="figure text-micro text-fern-500">{{ $index + 1 }}</span>
                <div class="min-w-0 grow">
                    <a href="{{ route('learner.courses.show', ['course' => $item['course']['slug']]) }}"
                       class="font-medium text-basalt-900 hover:text-accent-600">{{ $item['course']['title'] }}</a>
                    <p class="text-micro text-fern-500">{{ $item['platform_name'] }}</p>
                </div>
                <x-progress-bar :value="$item['progress']" size="sm" class="w-28" :show-value="false" />
                <x-status-badge :status="$item['state'] === 'not_started' ? 'draft' : $item['state']">
                    {{ $item['state'] === 'not_started' ? 'Not started' : ($item['state'] === 'in_progress' ? 'In progress' : 'Completed') }}
                </x-status-badge>
            </li>
        @endforeach
    </ol>
</x-layouts.learner>
