<x-layouts.public title="Certificate verification">
    <x-public.trail :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Certificate verification'],
    ]" />

    <div class="mx-auto max-w-xl">
        <h1 class="font-display text-title text-basalt-900">Certificate verification</h1>
        <p class="mt-3 text-read leading-relaxed text-fern-600">
            {{ $page['header']['subtitle'] ?? 'Enter the number printed on the certificate. No login is required. A valid result shows the learner’s name, the course, the platform and the issue date — nothing else.' }}
        </p>

        <div class="public-card mt-8 p-5 sm:p-8">
            <form method="GET" action="{{ route('certificates.lookup') }}">
                <x-field name="code" label="Certificate number" size="sm"
                         placeholder="OS-GEM-2026-1847"
                         autocomplete="off" />
                <div class="mt-4">
                    <x-button type="submit">Verify certificate</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.public>
