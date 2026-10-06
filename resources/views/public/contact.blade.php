<x-layouts.public title="Contact and support" flush>
    <x-public.section tone="white" class="grow">
        <x-public.trail :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Contact and support'],
        ]" />

        <div class="grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
            <x-public.page-heading
                title="Contact and support"
                :subtitle="$page['header']['subtitle'] ?? 'For enrolment, a lost certificate number, or a course that will not open, write to the school desk.'" />

            <dl class="public-card grid gap-6 p-6 text-dense sm:p-8">
                <div>
                    <dt class="text-micro text-fern-500">Email</dt>
                    <dd class="mt-1">
                        <a href="mailto:{{ $page['email'] }}" class="font-medium text-accent-700 hover:underline">{{ $page['email'] }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Telephone</dt>
                    <dd class="mt-1 text-basalt-900">{{ $page['phone'] }}</dd>
                </div>
            </dl>
        </div>
    </x-public.section>
</x-layouts.public>
