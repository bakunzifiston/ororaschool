@props(['title' => null, 'flush' => false])

@php
    $nav = \App\Support\DemoData\Navigation::public();
    $platforms = \App\Support\DemoData\PublicCatalog::activePlatforms();
@endphp

<x-layouts.shell experience="public" :title="$title"
                 content-class="{{ $flush ? 'max-w-none' : 'max-w-6xl' }}"
                 main-class="{{ $flush ? 'grow' : 'grow px-4 py-10 sm:px-6 lg:px-10 lg:py-14' }}">

    <x-slot:topbarStart>
        <x-layouts.partials.brand :href="route('home')" class="shrink-0" />

        <nav class="hidden min-w-0 flex-1 justify-center md:flex md:items-center md:gap-1" aria-label="Primary navigation">
            @foreach ($nav as $item)
                @php
                    $active = Route::has($item['route']) && request()->routeIs(
                        $item['route'],
                        $item['route'].'.*',
                        $item['route'] === 'certificates.lookup' ? 'certificates.verify' : $item['route'],
                    );
                @endphp

                <a href="{{ route($item['route']) }}"
                   @class([
                       'rounded-md px-2.5 py-1.5 text-dense transition-colors',
                       'bg-accent-50 font-medium text-accent-700' => $active,
                       'text-fern-500 hover:bg-clay-100 hover:text-basalt-900' => ! $active,
                   ])
                   @if ($active) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <button type="button" x-on:click="nav = ! nav"
                :aria-expanded="nav ? 'true' : 'false'"
                class="ml-auto inline-flex h-9 items-center gap-1.5 rounded-md border border-clay-200 bg-chalk px-2.5 text-dense text-fern-500 md:hidden">
            <x-icon name="menu" class="h-4 w-4" />
            Menu
        </button>
    </x-slot:topbarStart>

    <x-slot:topbarEnd>
        <span class="hidden items-center gap-2 md:flex">
            <x-button variant="secondary" :href="route('login')">Sign in</x-button>
            <x-button :href="route('register')">Get started</x-button>
        </span>
    </x-slot:topbarEnd>

    <x-slot:mobilePanel>
        <div x-show="nav" x-cloak class="border-b border-clay-200 bg-chalk px-4 py-3 md:hidden">
            <nav aria-label="Primary navigation">
                <ul class="grid gap-0.5">
                    @foreach ($nav as $item)
                        @php
                            $active = Route::has($item['route']) && request()->routeIs(
                                $item['route'],
                                $item['route'].'.*',
                                $item['route'] === 'certificates.lookup' ? 'certificates.verify' : $item['route'],
                            );
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'flex items-center gap-2.5 rounded-md px-2 py-2 text-body',
                                   'bg-accent-50 font-medium text-accent-700' => $active,
                                   'text-basalt-800 hover:bg-papyrus' => ! $active,
                               ])
                               @if ($active) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="h-4 w-4 text-fern-500" />
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="mt-3 grid gap-2 border-t border-clay-100 pt-3">
                <x-button variant="secondary" :href="route('login')" class="w-full">Sign in</x-button>
                <x-button :href="route('register')" class="w-full">Get started</x-button>
            </div>
        </div>
    </x-slot:mobilePanel>

    {{ $slot }}

    <x-slot:footer>
        <footer class="mt-auto border-t border-clay-200 bg-basalt-900 text-chalk">
            <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-10 lg:py-14">
                <div>
                    <x-layouts.partials.brand :href="route('home')" on-basalt />
                    <p class="mt-3 max-w-xs text-dense leading-relaxed text-clay-200">
                        Training and certification for farmers, livestock producers and field officers
                        across the Orora ecosystem.
                    </p>
                </div>

                <div>
                    <p class="text-micro font-medium text-chalk">Platforms</p>
                    <ul class="mt-3 grid gap-2">
                        @foreach ($platforms as $platform)
                            <li>
                                <a href="{{ $platform['href'] }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">{{ $platform['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="text-micro font-medium text-chalk">Learning</p>
                    <ul class="mt-3 grid gap-2">
                        <li>
                            <a href="{{ route('catalog.courses') }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">Courses</a>
                        </li>
                        <li>
                            <a href="{{ route('certificates.lookup') }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">Certificate verification</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="text-micro font-medium text-chalk">Support</p>
                    <ul class="mt-3 grid gap-2">
                        <li>
                            <a href="{{ route('contact') }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">Contact / support</a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">About Orora School</a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="text-dense text-clay-200 transition-colors hover:text-chalk">Sign in</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-basalt-700">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-4 sm:px-6 lg:px-10">
                    <p class="text-micro text-clay-200">© {{ now()->year }} Orora School. All rights reserved.</p>
                    <p class="text-micro text-clay-200">Demonstration build · fixture data</p>
                </div>
            </div>
        </footer>
    </x-slot:footer>
</x-layouts.shell>
