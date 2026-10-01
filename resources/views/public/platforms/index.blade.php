<x-layouts.public title="Platforms" flush>
    <x-public.section tone="terrace" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Platforms'],
        ]" />

        <x-public.page-heading
            on-dark
            title="Platforms"
            subtitle="Four live platforms. Each teaches the work of its own system, on one learning record." />
    </x-public.section>

    <x-public.section tone="chalk" class="grow">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            @foreach ($page['platforms'] as $platform)
                <x-public.platform-card :platform="$platform" detail />
            @endforeach
        </div>
    </x-public.section>
</x-layouts.public>
