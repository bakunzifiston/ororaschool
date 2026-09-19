<x-layouts.guest title="Email confirmed">
    <x-auth-sheet :title="$page['title']" :subtitle="$page['subtitle']">
        {{-- The one confirmation mark in the interface. A drawn rule and the ok
             hue, not a full-bleed green banner. --}}
        <p class="mb-5 inline-flex items-center gap-2 rounded-sm border border-ok/30 bg-ok-bg px-2.5 py-1.5 text-dense font-medium text-ok">
            <x-icon name="check" class="h-4 w-4" />
            Verified
        </p>

        <ul class="grid gap-2.5">
            @foreach ($page['next'] as $item)
                <li class="flex items-start gap-2.5 text-dense leading-relaxed text-fern-600">
                    <x-icon name="arrow-right" class="mt-1 h-3.5 w-3.5 shrink-0 text-accent-500" />
                    {{ $item }}
                </li>
            @endforeach
        </ul>

        <x-button :href="$dashboardUrl" size="lg" icon-after="arrow-right" class="mt-6 w-full">
            {{ $page['submit'] }}
        </x-button>

        <x-slot:footer>
            <span class="text-micro">
                Opening the {{ $previewLabel }} shell — the preview chosen at sign-in.
            </span>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
