<x-layouts.learner :title="$page['resource']['title']" width="wide">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['resource']['download_url'] ?? null)
                <x-button variant="secondary" size="sm" icon="arrow-down" :href="$page['resource']['download_url']">Download</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-resource-reader :resource="$page['resource']" :chrome="false" />
    </div>
</x-layouts.learner>
