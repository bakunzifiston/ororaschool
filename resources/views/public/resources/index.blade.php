<x-layouts.public title="Resources" flush>
    <x-public.section tone="terrace" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Resources'],
        ]" />

        <x-public.page-heading
            on-dark
            title="Resources"
            subtitle="Open academy handouts. Course, module and lesson files stay with enrolment." />
    </x-public.section>

    <x-public.section tone="chalk" class="grow">
        @php
            $hasActiveFilters = filled($page['filters']['q'] ?? '')
                || filled($page['filters']['platform'] ?? '')
                || filled($page['filters']['type'] ?? '');
        @endphp

        <form method="GET" action="{{ $page['formAction'] }}" class="public-filters">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative min-w-0 flex-1">
                    <label for="public-resource-q" class="sr-only">Search</label>
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-fern-500" />
                    <input id="public-resource-q"
                           type="search"
                           name="q"
                           value="{{ $page['filters']['q'] }}"
                           placeholder="Search resources"
                           class="h-11 w-full rounded-full border border-clay-200 bg-chalk py-0 pr-4 pl-10 text-dense text-basalt-800 placeholder:text-fern-500">
                </div>
                <x-button type="submit" class="w-full sm:w-auto">Search</x-button>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <label for="public-resource-platform" class="sr-only">Academy</label>
                <select id="public-resource-platform"
                        name="platform"
                        onchange="this.form.submit()"
                        @class([
                            'h-11 min-w-0 flex-1 rounded-full border px-3.5 text-dense sm:max-w-44 sm:flex-none',
                            'border-accent-200 bg-accent-50 text-accent-700' => filled($page['filters']['platform']),
                            'border-clay-200 bg-chalk text-basalt-800' => blank($page['filters']['platform']),
                        ])>
                    @foreach ($page['options']['platforms'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) $value === (string) $page['filters']['platform'])>{{ $label }}</option>
                    @endforeach
                </select>

                <label for="public-resource-type" class="sr-only">Type</label>
                <select id="public-resource-type"
                        name="type"
                        onchange="this.form.submit()"
                        @class([
                            'h-11 min-w-0 flex-1 rounded-full border px-3.5 text-dense sm:max-w-44 sm:flex-none',
                            'border-accent-200 bg-accent-50 text-accent-700' => filled($page['filters']['type']),
                            'border-clay-200 bg-chalk text-basalt-800' => blank($page['filters']['type']),
                        ])>
                    @foreach ($page['options']['types'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) $value === (string) $page['filters']['type'])>{{ $label }}</option>
                    @endforeach
                </select>

                @if ($hasActiveFilters)
                    <a href="{{ $page['formAction'] }}" class="inline-flex h-11 items-center px-2 text-dense font-medium text-accent-700 hover:underline">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-10">
            @if (count($page['resources']))
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($page['resources'] as $resource)
                        <x-public.resource-card :resource="$resource" />
                    @endforeach
                </div>

                @if (($page['pagination']['pages'] ?? 1) > 1)
                    <x-pagination :pagination="$page['pagination']" class="mt-10 px-0" />
                @endif
            @else
                <x-empty-state icon="search" :title="$page['emptyTitle']" :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button variant="secondary" :href="$page['formAction']">Clear filters</x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </div>
    </x-public.section>
</x-layouts.public>
