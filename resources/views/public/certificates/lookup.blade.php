<x-layouts.public title="Certificate verification" flush>
    <x-public.section tone="forest" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Certificate verification'],
        ]" />

        <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
            <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
            FARMSCHOOL
        </p>

        <x-public.page-heading
            class="mt-6"
            on-dark
            :title="$page['header']['title']"
            :subtitle="$page['header']['subtitle']" />
    </x-public.section>

    <x-public.section tone="white" class="grow">
        <form method="GET" action="{{ route('certificates.lookup') }}" class="public-card mx-auto max-w-xl p-6 sm:p-8">
            <x-field name="code" label="Certificate number"
                     placeholder="OS-GEM-2026-1847"
                     autocomplete="off" />
            <div class="mt-5">
                <x-button type="submit" size="lg" class="w-full sm:w-auto">Verify certificate</x-button>
            </div>
        </form>
    </x-public.section>
</x-layouts.public>
