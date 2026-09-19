<x-layouts.guest title="Certificate {{ $page['code'] }}">
    <x-auth-sheet :title="$page['title']" :subtitle="$page['subtitle']">
        @if ($page['found'])
            <dl class="grid gap-3 text-dense">
                <div>
                    <dt class="text-micro text-fern-500">Code</dt>
                    <dd class="font-mono text-basalt-900">{{ $page['code'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Learner</dt>
                    <dd class="text-basalt-900">{{ $page['learner'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Course</dt>
                    <dd class="text-basalt-900">{{ $page['course'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Issued</dt>
                    <dd class="text-basalt-900">{{ $page['issued_at'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Platform</dt>
                    <dd class="text-basalt-900">{{ $page['platform'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Status</dt>
                    <dd class="mt-1"><x-status-badge :status="$page['status']" /></dd>
                </div>
            </dl>
        @else
            <p class="text-dense text-fern-600">{{ $page['message'] }}</p>
        @endif
    </x-auth-sheet>
</x-layouts.guest>
