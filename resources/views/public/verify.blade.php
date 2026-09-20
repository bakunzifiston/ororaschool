<x-layouts.public title="Certificate {{ $page['code'] }}">
    @php
        $status = $page['status'] ?? ($page['found'] ? ($page['valid'] ? 'valid' : 'revoked') : 'not_found');
    @endphp

    <x-public.trail :items="[
        ['label' => 'Certificate verification', 'route' => 'certificates.lookup'],
        ['label' => $page['header']['title'] ?? $page['code']],
    ]" />

    <div class="mx-auto max-w-xl">
        <h1 class="font-display text-title text-basalt-900">{{ $page['header']['title'] ?? 'Certificate '.$page['code'] }}</h1>
        @if (($page['header']['subtitle'] ?? $page['subtitle'] ?? '') !== '')
            <p class="mt-3 text-read leading-relaxed text-fern-600">{{ $page['header']['subtitle'] ?? $page['subtitle'] }}</p>
        @endif

        <div class="public-card mt-8 p-5 sm:p-8">
            <x-status-badge :status="$status" />

            @if (! $page['found'])
                <p class="mt-4 text-read leading-relaxed text-fern-600">{{ $page['message'] }}</p>
            @else
                <dl class="mt-6 grid gap-4 text-dense sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <dt class="text-micro text-fern-500">Learner</dt>
                        <dd class="mt-1 font-display text-panel text-basalt-900">{{ $page['learner'] }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-micro text-fern-500">Course</dt>
                        <dd class="mt-1 text-basalt-900">{{ $page['course'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-micro text-fern-500">Platform</dt>
                        <dd class="mt-1 text-basalt-900">{{ $page['platform'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-micro text-fern-500">Issued</dt>
                        <dd class="mt-1 text-basalt-900">{{ $page['issued_at'] }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-micro text-fern-500">Code</dt>
                        <dd class="mt-1 font-mono text-basalt-900">{{ $page['code'] }}</dd>
                    </div>
                </dl>
            @endif
        </div>

        <p class="mt-6">
            <a href="{{ route('certificates.lookup') }}" class="inline-flex items-center gap-1 text-dense font-medium text-accent-700 hover:underline">
                Verify another certificate
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </p>
    </div>
</x-layouts.public>
