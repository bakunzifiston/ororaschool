<x-layouts.public title="Platforms">
    <x-public.trail :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Platforms'],
    ]" />

    <h1 class="font-display text-title text-basalt-900">Platforms</h1>
    <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">
        Four live platforms. Each teaches the work of its own system, on one learning record.
    </p>

    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($page['platforms'] as $platform)
            <x-public.platform-card :platform="$platform" detail />
        @endforeach
    </div>
</x-layouts.public>
