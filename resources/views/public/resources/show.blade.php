<x-layouts.public :title="$page['resource']['title']" flush>
    <x-public.section tone="chalk" compact class="grow">
        <x-public.trail :items="[
            ['label' => 'Resources', 'route' => 'catalog.resources'],
            ['label' => $page['resource']['title']],
        ]" />

        <x-resource-reader :resource="$page['resource']" />
    </x-public.section>
</x-layouts.public>
