<x-layouts.platform-workspace title="Categories" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.categories', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if (! count($page['tree']))
            <x-panel :padded="false">
                <x-empty-state icon="folder"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @else
            <div class="grid gap-4">
                @foreach ($page['tree'] as $academy)
                    @php
                        $categoryCount = count($academy['categories']);
                        $childCount = 0;
                        foreach ($academy['categories'] as $node) {
                            $childCount += count($node['children'] ?? []);
                        }
                        $subtitle = $categoryCount.' '.($categoryCount === 1 ? 'category' : 'categories');
                        if ($childCount > 0) {
                            $subtitle .= ' · '.$childCount.' '.($childCount === 1 ? 'sub-category' : 'sub-categories');
                        }
                    @endphp

                    <x-panel :title="$academy['name']" :subtitle="$subtitle">
                        <x-slot:actions>
                            <span class="inline-flex items-center rounded-full bg-[#e4f6d6] px-2.5 py-0.5 text-micro font-medium text-[#4f9a28]">Academy</span>
                            <x-button variant="secondary" size="sm" icon="plus"
                                      x-on:click="$dispatch('open-modal', 'add-category-{{ $academy['slug'] }}')">
                                Add category
                            </x-button>
                        </x-slot:actions>

                        <ul class="divide-y divide-clay-100">
                            @foreach ($academy['categories'] as $category)
                                <li class="py-3 first:pt-0 last:pb-0">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <span class="text-fern-400" aria-hidden="true">
                                            <x-icon name="grip" class="h-3.5 w-3.5" />
                                        </span>
                                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e4f6d6] text-[#4f9a28]" aria-hidden="true">
                                            <x-icon name="folder" class="h-3.5 w-3.5" />
                                        </span>
                                        <p class="min-w-0 grow text-dense font-medium text-basalt-900">{{ $category['name'] }}</p>
                                        <span class="shrink-0 text-micro text-fern-500">Category</span>
                                        <x-button variant="ghost" size="sm" icon="plus"
                                                  x-on:click="$dispatch('open-modal', 'add-child-{{ $category['slug'] }}')">
                                            Add sub-category
                                        </x-button>
                                        <x-button variant="danger" size="sm"
                                                  x-on:click="$dispatch('open-modal', 'delete-{{ $category['slug'] }}')">
                                            Delete
                                        </x-button>
                                    </div>

                                    @if (count($category['children'] ?? []))
                                        <ul class="mt-2 ml-11 space-y-0.5 border-l border-clay-200 pl-4">
                                            @foreach ($category['children'] as $child)
                                                <li class="flex flex-wrap items-center gap-2 py-1.5">
                                                    <span class="text-fern-400" aria-hidden="true">
                                                        <x-icon name="grip" class="h-3 w-3" />
                                                    </span>
                                                    <span class="min-w-0 grow text-dense text-basalt-800">{{ $child['name'] }}</span>
                                                    <span class="shrink-0 text-micro text-fern-500">Sub-category</span>
                                                    <x-button variant="danger" size="sm"
                                                              x-on:click="$dispatch('open-modal', 'delete-{{ $child['slug'] }}')">
                                                        Delete
                                                    </x-button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </x-panel>
                @endforeach
            </div>
        @endif
    </div>

    @foreach ($page['tree'] as $academy)
        <x-modal :name="'add-category-'.$academy['slug']" width="md"
                 :title="'Add a category to '.$academy['name']"
                 subtitle="The new category sits under this academy.">
            <form method="POST" action="{{ route('workspace.categories.store', ['platform' => $platformSlug]) }}" class="grid gap-4">
                @csrf
                <input type="hidden" name="academy" value="{{ $academy['slug'] }}">
                <x-field name="name" label="Category name" size="sm" required />
                <div class="flex justify-end gap-2">
                    <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                    <x-button type="submit" icon="plus">Add category</x-button>
                </div>
            </form>
        </x-modal>

        @foreach ($academy['categories'] as $category)
            <x-modal :name="'add-child-'.$category['slug']" width="md"
                     :title="'Add a sub-category to '.$category['name']"
                     subtitle="The new sub-category sits under this category.">
                <form method="POST" action="{{ route('workspace.categories.store', ['platform' => $platformSlug]) }}" class="grid gap-4">
                    @csrf
                    <input type="hidden" name="academy" value="{{ $academy['slug'] }}">
                    <input type="hidden" name="parent" value="{{ $category['slug'] }}">
                    <x-field name="name" label="Sub-category name" size="sm" required />
                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <x-button type="submit">Add sub-category</x-button>
                    </div>
                </form>
            </x-modal>

            <x-modal :name="'delete-'.$category['slug']" width="md"
                     :title="'Delete '.$category['name'].'?'"
                     subtitle="Category">
                <p>{{ $category['name'] }} will leave this academy, along with any sub-categories under it.</p>
                <x-slot:actions>
                    <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                    <form method="POST" action="{{ route('workspace.categories.destroy', ['platform' => $platformSlug, 'category' => $category['slug']]) }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">Delete category</x-button>
                    </form>
                </x-slot:actions>
            </x-modal>

            @foreach ($category['children'] ?? [] as $child)
                <x-modal :name="'delete-'.$child['slug']" width="md"
                         :title="'Delete '.$child['name'].'?'"
                         subtitle="Sub-category">
                    <p>{{ $child['name'] }} will leave this academy.</p>
                    <x-slot:actions>
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('workspace.categories.destroy', ['platform' => $platformSlug, 'category' => $child['slug']]) }}">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger">Delete sub-category</x-button>
                        </form>
                    </x-slot:actions>
                </x-modal>
            @endforeach
        @endforeach
    @endforeach
</x-layouts.platform-workspace>
