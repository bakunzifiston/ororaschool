@props([
    'experience' => 'super-admin',
    'title' => null,
    'railed' => false,
    'contentClass' => 'max-w-6xl',
    'mainClass' => 'min-w-0 grow px-4 py-6 sm:px-6 lg:px-8 lg:py-10',
    'user' => [],
])

{{--
    The one shell every layout is built from: document head, top bar structure,
    mobile drawer plumbing, flash region and footer all live here. The three
    layouts differ only in what they pass into the sidebar and top-bar slots,
    so there is nothing to keep in sync by hand.
--}}
<!DOCTYPE html>
<html lang="en" data-experience="{{ $experience }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/brand/favicon.png') }}" type="image/png">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Declared explicitly, not left to auto-injection: Livewire only injects
         when a component is on the page, which would leave Alpine — and so the
         modal, dropdown and drawer — missing from pages that have none. --}}
    @livewireStyles
</head>
<body class="flex min-h-full flex-col antialiased" x-data="{ nav: false }">

    @if ($railed)
        {{-- Mobile bar. The rail becomes a drawer below lg. --}}
        <div class="sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between gap-3 border-b border-clay-200 bg-chalk/90 px-3 backdrop-blur lg:hidden">
            <button type="button" x-on:click="nav = true"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-clay-200 text-fern-500"
                    aria-label="Open navigation">
                <x-icon name="menu" class="h-4 w-4" />
            </button>

            {{-- Below lg the top bar is gone, so whatever matters most for
                 orientation moves here. The workspace layout puts its platform
                 switcher in this slot. --}}
            @isset($mobileBrand)
                {{ $mobileBrand }}
            @else
                <x-layouts.partials.brand />
            @endisset

            <x-layouts.partials.user-menu :user="$user" compact menu-id="mobile-account-menu" />
        </div>

        <div x-show="nav" x-cloak x-on:click="nav = false"
             class="fixed inset-0 z-30 bg-basalt-950/50 lg:hidden" aria-hidden="true"></div>

        {{-- Hidden below lg by default so the rail renders without waiting on
             Alpine; the drawer toggle forces it visible. --}}
        <aside class="on-basalt fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-basalt-900 lg:flex lg:w-64"
               :class="nav && '!flex'"
               aria-label="Primary navigation">
            {{ $sidebar }}
        </aside>
    @endif

    <div @class(['flex min-w-0 grow flex-col', 'lg:pl-64' => $railed])>

        {{-- Full-bleed band above the flash region. The guest layout uses it for
             its basalt masthead; the app layouts have a top bar instead. --}}
        @isset($masthead)
            {{ $masthead }}
        @endisset

        @if (isset($topbarStart) || isset($topbarEnd))
        <header @class([
            'sticky top-0 z-20 h-14 shrink-0 items-center gap-4 border-b border-clay-200 bg-chalk/90 px-4 backdrop-blur sm:px-6',
            'hidden lg:flex lg:px-8' => $railed,
            'flex' => ! $railed,
        ])>
            @isset($topbarStart)
                {{ $topbarStart }}
            @endisset

            <div class="ml-auto flex shrink-0 items-center">
                @isset($topbarEnd)
                    {{ $topbarEnd }}
                @endisset
            </div>
        </header>
        @endif

        @isset($mobilePanel)
            {{ $mobilePanel }}
        @endisset

        <x-layouts.partials.flash />

        <main class="{{ $mainClass }}">
            <div class="mx-auto min-w-0 {{ $contentClass }}">
                {{ $slot }}
            </div>
        </main>

        @isset($footer)
            {{ $footer }}
        @else
            <x-layouts.partials.footer :experience="$experience" />
        @endisset
    </div>

    @livewireScripts
</body>
</html>
