<x-layouts.platform-workspace :title="$page['header']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST" action="{{ route('workspace.resources.store', ['platform' => $platformSlug]) }}" class="mt-6 max-w-2xl">
        @csrf

        <x-panel title="File">
            <div class="grid gap-5">
                <x-field name="title" label="Title" size="sm" placeholder="e.g. CMT field sheet" />

                <x-select name="type" label="Type" size="sm"
                          :options="array_filter($page['types'], fn ($label, $key) => $key !== '', ARRAY_FILTER_USE_BOTH)" />

                <x-select name="attached_kind" label="Attach to" size="sm" :options="$page['kinds']" />

                <div>
                    <p class="mb-1.5 text-dense font-medium text-basalt-800">File</p>
                    <div class="flex items-center gap-3 rounded-md border border-dashed border-clay-300 bg-papyrus px-4 py-5">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-md border border-clay-200 bg-chalk text-fern-500">
                            <x-icon name="upload" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-dense text-basalt-800">Drop a file, or choose one</p>
                            <p class="mt-0.5 text-micro text-fern-500">PDF, video or image. Upload is a placeholder in this build.</p>
                        </div>
                    </div>
                    <input type="file" name="file" class="sr-only" aria-label="Resource file">
                </div>
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">Save resource</x-button>
            <x-button variant="secondary" :href="route('workspace.resources', ['platform' => $platformSlug])">Cancel</x-button>
        </div>
    </form>
</x-layouts.platform-workspace>
