<x-layouts.public title="About FarmSchool" flush>
    <x-public.section tone="accent" compact>
        <x-public.trail :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'About FarmSchool'],
        ]" />

        <x-public.page-heading
            title="About FarmSchool"
            :subtitle="$page['header']['subtitle'] ?? 'The training and certification layer for the Orora ecosystem.'" />
    </x-public.section>

    <x-public.section tone="chalk" class="grow">
        <div class="grid items-start gap-12 lg:grid-cols-2 lg:gap-16">
            <article>
                <p class="text-read leading-relaxed text-fern-600">
                    Field officers, collection-centre staff, livestock keepers and smallholders take courses
                    that match the work in front of them — plot books on OroraFarm, milk hygiene on Gemura,
                    ear tags on BuchaPro, rations on FeedGrid.
                </p>
                <p class="mt-4 text-read leading-relaxed text-fern-600">
                    The architecture decision you can see on this site: one person has one learning record.
                    Academies are labels on courses, not separate accounts. A certificate minted on Gemura
                    is still yours when you later train on BuchaPro.
                </p>
                <p class="mt-4 text-read leading-relaxed text-fern-600">
                    This demonstration build uses fixture data. Nothing here is a live enrolment.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-button :href="route('catalog.courses')">Explore courses</x-button>
                    <x-button variant="secondary" :href="route('register')">Create an account</x-button>
                </div>
            </article>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach ($page['platforms'] as $platform)
                    <x-public.platform-card :platform="$platform" />
                @endforeach
            </div>
        </div>
    </x-public.section>
</x-layouts.public>
