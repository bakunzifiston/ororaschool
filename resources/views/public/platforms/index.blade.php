<x-layouts.public title="Academies" flush>
    <x-public.section tone="forest" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Academies'],
        ]" />

        <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
            <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
            FARMSCHOOL
        </p>

        <x-public.page-heading
            class="mt-6"
            on-dark
            :title="$page['title']"
            :subtitle="$page['subtitle']" />
    </x-public.section>

    <x-public.section tone="papyrus" class="grow">
        @if (count($page['platforms']))
            <p class="text-dense text-fern-500">
                {{ number_format(count($page['platforms'])) }}
                {{ count($page['platforms']) === 1 ? 'academy' : 'academies' }}
            </p>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($page['platforms'] as $platform)
                    <x-public.platform-card :platform="$platform" detail />
                @endforeach
            </div>
        @endif
    </x-public.section>
</x-layouts.public>
