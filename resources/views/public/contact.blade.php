<x-layouts.public title="Contact and support">
    <x-public.trail :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Contact and support'],
    ]" />

    <h1 class="font-display text-title text-basalt-900">Contact and support</h1>
    <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">
        Your field coordinator can usually resolve a roster question faster than a public form.
    </p>

    <dl class="mt-8 grid max-w-xl gap-4 text-dense">
        <div>
            <dt class="text-micro text-fern-500">Email</dt>
            <dd class="mt-0.5">
                <a href="mailto:{{ $page['email'] }}" class="font-medium text-accent-700 hover:underline">{{ $page['email'] }}</a>
            </dd>
        </div>
        <div>
            <dt class="text-micro text-fern-500">Telephone</dt>
            <dd class="mt-0.5 text-basalt-900">{{ $page['phone'] }}</dd>
        </div>
    </dl>
</x-layouts.public>
