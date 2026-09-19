<x-layouts.platform-workspace :title="$label" :platform="$platformSlug">
    <x-page-header :breadcrumb="$breadcrumb" :title="$label"
                   subtitle="Scoped to {{ $platform['name'] }}. Switch platforms in the top bar and this destination follows you." />

    <div class="mt-6">
        <x-panel variant="quiet" :padded="false">
            <x-empty-state icon="sprout"
                           title="{{ $label }} for {{ $platform['name'] }} is not built yet"
                           message="The rail, the switcher and the shared components are in place. {{ $label }} gets its real screen when {{ $platform['name'] }}'s records exist to fill it.">
                <x-slot:actions>
                    <x-button :href="route('workspace.dashboard', ['platform' => $platformSlug])" variant="secondary" icon="chevron-left">
                        Back to the {{ $platform['name'] }} dashboard
                    </x-button>
                    <x-button :href="route('design.components', ['experience' => 'platform-workspace'])" variant="ghost">See the component set</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
