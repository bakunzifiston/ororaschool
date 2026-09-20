<x-layouts.super-admin title="Roles">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.roles', ['empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('admin.roles.create')">New custom role</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if (! count($page['roles']))
            <x-panel :padded="false">
                <x-empty-state icon="shield"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('admin.roles.create')">New custom role</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @else
            <div class="grid grid-cols-1 gap-3">
                @foreach ($page['roles'] as $role)
                    <a href="{{ route('admin.roles.edit', $role['key']) }}"
                       class="flex flex-wrap items-start justify-between gap-4 rounded-md border border-clay-200 bg-chalk px-4 py-3.5 transition-colors hover:border-clay-300">
                        <div class="min-w-0 grow">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-role-chip :role="$role['key']" />
                                @if ($role['system'])
                                    <span class="inline-flex items-center gap-1 rounded-sm border border-clay-200 bg-papyrus px-1.5 py-0.5 text-micro text-fern-500">
                                        <x-icon name="lock" class="h-3 w-3" />
                                        System-protected
                                    </span>
                                @else
                                    <span class="text-micro text-fern-500">Custom</span>
                                @endif
                            </div>
                            <p class="mt-2 max-w-2xl text-dense leading-relaxed text-fern-500">{{ $role['description'] }}</p>
                        </div>
                        <p class="figure shrink-0 text-micro text-fern-500">{{ number_format($role['holders']) }} holders</p>
                    </a>
                @endforeach
            </div>

            <x-pagination :pagination="$page['pagination']" class="mt-3 rounded-md border border-clay-200 bg-chalk" />
        @endif
    </div>
</x-layouts.super-admin>
