<x-layouts.super-admin :title="$label">
    <x-page-header :breadcrumb="$breadcrumb" :title="$label"
                   subtitle="This destination is wired up and reachable. The screen itself is built in a later phase." />

    <div class="mt-6">
        <x-panel variant="quiet" :padded="false">
            <x-empty-state icon="sprout"
                           title="{{ $label }} is not built yet"
                           message="Phase F1 delivers the shell: layouts, navigation and the shared component set. {{ $label }} gets its real screen once the backend phases supply records to put on it.">
                <x-slot:actions>
                    <x-button :href="route('admin.dashboard')" variant="secondary" icon="chevron-left">Back to the dashboard</x-button>
                    <x-button :href="route('design.components', ['experience' => 'super-admin'])" variant="ghost">See the component set</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-panel>
    </div>
</x-layouts.super-admin>
