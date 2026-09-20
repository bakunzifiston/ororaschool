<x-layouts.public title="About Orora School">
    <x-public.trail :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About Orora School'],
    ]" />

    <h1 class="font-display text-title text-basalt-900">About Orora School</h1>

    <article class="mt-6 max-w-xl">
        <p class="text-read leading-relaxed text-fern-600">
            Field officers, collection-centre staff, livestock keepers and smallholders take courses
            that match the work in front of them — plot books on OroraFarm, milk hygiene on Gemura,
            ear tags on BuchaPro, rations on FeedGrid.
        </p>
        <p class="mt-4 text-read leading-relaxed text-fern-600">
            The architecture decision you can see on this site: one person has one learning record.
            Platforms are labels on courses, not separate accounts. A certificate minted on Gemura
            is still yours when you later train on BuchaPro.
        </p>
        <p class="mt-4 text-read leading-relaxed text-fern-600">
            This demonstration build uses fixture data. Nothing here is a live enrolment.
        </p>
        <div class="mt-8">
            <x-button :href="route('catalog.courses')">Explore courses</x-button>
        </div>
    </article>
</x-layouts.public>
