<x-layouts.learner :title="$label">
    <x-page-header :breadcrumb="$breadcrumb" :title="$label"
                   subtitle="This part of your account is on the way." />

    <div class="mt-6">
        <x-panel variant="quiet" :padded="false">
            <x-empty-state icon="sprout"
                           title="{{ $label }} is coming soon"
                           message="We are still building this section. In the meantime your courses and progress are on the dashboard, and anything you have already finished stays saved.">
                <x-slot:actions>
                    <x-button :href="route('learner.dashboard')" variant="secondary" icon="chevron-left">Back to my dashboard</x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-panel>
    </div>
</x-layouts.learner>
