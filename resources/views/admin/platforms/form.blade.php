<x-layouts.super-admin :title="$page['header']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST"
          action="{{ $page['isEdit'] ? route('admin.platforms.update', $page['platform']['slug']) : route('admin.platforms.store') }}"
          class="mt-6 max-w-2xl">
        @csrf

        <x-panel title="Platform record">
            <div class="grid gap-5">
                <x-field name="name" label="Name" size="sm"
                         :value="$page['platform']['name']"
                         placeholder="e.g. FeedGrid" />

                <x-field name="slug" label="Slug" size="sm"
                         :value="$page['platform']['slug']"
                         :readonly="$page['isEdit']"
                         hint="Used in workspace URLs. Locked after create so existing links keep working."
                         placeholder="e.g. feedgrid" />

                <div>
                    <p class="mb-1.5 text-dense font-medium text-basalt-800">Logo</p>
                    <div class="flex items-center gap-3 rounded-md border border-dashed border-clay-300 bg-papyrus px-4 py-5">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-md border border-clay-200 bg-chalk text-fern-500">
                            <x-icon name="upload" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-dense text-basalt-800">Drop a square mark, or choose a file</p>
                            <p class="mt-0.5 text-micro text-fern-500">PNG or SVG, 512×512. Upload is a placeholder in this build.</p>
                        </div>
                    </div>
                    <input type="file" name="logo" accept="image/png,image/svg+xml" class="sr-only" aria-label="Platform logo">
                </div>

                <x-textarea name="description" label="Description"
                            :value="$page['platform']['description'] ?? ''"
                            hint="Shown on the estate list and inside the workspace switcher." />

                <x-toggle name="active" label="Platform is active"
                          :checked="($page['platform']['status'] ?? 'inactive') === 'active'"
                          hint="Inactive platforms stay in the list so certificates still resolve, but staff cannot open the workspace." />
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">{{ $page['isEdit'] ? 'Save platform' : 'Create platform' }}</x-button>
            <x-button variant="secondary" :href="route('admin.platforms')">Cancel</x-button>
        </div>
    </form>
</x-layouts.super-admin>
