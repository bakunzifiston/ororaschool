<x-layouts.public title="Certificate verification" flush>
    <x-public.section tone="terrace" compact>
        <x-public.trail on-dark :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Certificate verification'],
        ]" />

        <x-public.page-heading
            on-dark
            title="Certificate verification"
            :subtitle="$page['header']['subtitle']" />
    </x-public.section>

    <x-public.section tone="chalk" class="grow">
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
