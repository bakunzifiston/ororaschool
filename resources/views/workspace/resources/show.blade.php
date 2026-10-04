<x-layouts.platform-workspace :title="$page['resource']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['resource']['download_url'] ?? null)
                <x-button variant="secondary" size="sm" icon="arrow-down" :href="$page['resource']['download_url']">Download</x-button>
            @endif
            <x-button variant="secondary" size="sm" :href="$page['resource']['edit']">Edit</x-button>
            <x-button variant="danger" size="sm" x-on:click="$dispatch('open-modal', '{{ $page['resource']['delete'] }}')">Delete</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-resource-reader :resource="$page['resource']" :chrome="false" />
    </div>

    <x-modal :name="$page['resource']['delete']" width="md"
             :title="'Delete '.$page['resource']['title'].'?'"
             :subtitle="$page['resource']['type_label'].' · '.$page['resource']['attached_to']">
        <p>{{ $page['resource']['title'] }} will leave this academy and the public catalogue if it was an open handout.</p>

        <x-slot:actions>
            <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
            <form method="POST" action="{{ $page['resource']['destroy'] }}">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Delete resource</x-button>
            </form>
        </x-slot:actions>
    </x-modal>
</x-layouts.platform-workspace>
