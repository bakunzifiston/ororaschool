<x-layouts.guest title="Get your account" wide>
    <div class="overflow-hidden rounded-3xl border border-clay-200 bg-chalk shadow-lg">
        <div class="grid lg:grid-cols-2">
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-12 lg:py-14">
                <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-fern-500">
                    <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
                    FARMSCHOOL
                </p>

                <h1 class="mt-5 font-display text-title tracking-tight text-basalt-900">{{ $page['title'] }}</h1>

                <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-5">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field name="first_name" appearance="pill"
                                 :label="$page['fields']['first_name']['label']"
                                 :placeholder="$page['fields']['first_name']['placeholder']"
                                 autocomplete="given-name"
                                 autofocus />

                        <x-field name="last_name" appearance="pill"
                                 :label="$page['fields']['last_name']['label']"
                                 :placeholder="$page['fields']['last_name']['placeholder']"
                                 autocomplete="family-name" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2"
                         x-data="{
                             district: @js(old('district', '')),
                             sector: @js(old('sector', '')),
                             map: @js($page['sectorsByDistrict']),
                             get sectorOptions() {
                                 return this.map[this.district] ?? [];
                             },
                         }"
                         x-on:change="if ($event.target.name === 'district' && ! sectorOptions.includes(sector)) { sector = '' }">
                        <x-select name="district" appearance="pill"
                                  :label="$page['fields']['district']['label']"
                                  :options="$page['districts']"
                                  placeholder="Choose your district"
                                  model="district" />

                        <x-select name="sector" appearance="pill"
                                  :label="$page['fields']['sector']['label']"
                                  placeholder="Choose your sector"
                                  model="sector"
                                  disable-when="! district"
                                  alpine-options="sectorOptions" />
                    </div>

                    <x-field name="email" type="email" appearance="pill"
                             :label="$page['fields']['email']['label']"
                             :placeholder="$page['fields']['email']['placeholder']"
                             autocomplete="email" />

                    <x-field name="password" type="password" appearance="pill"
                             :label="$page['fields']['password']['label']"
                             autocomplete="new-password"
                             revealable />

                    <x-button type="submit" size="lg" variant="forest" class="w-full">
                        {{ $page['submit'] }}
                    </x-button>
                </form>

                <p class="mt-8 text-center text-dense text-fern-500">
                    {{ $page['footer'] }}
                    <a href="{{ route('login') }}" class="font-medium text-accent-600 underline-offset-2 hover:underline">
                        {{ $page['footerLink'] }}
                    </a>
                </p>
            </div>

            <div class="relative hidden min-h-[28rem] lg:block">
                <img src="{{ asset('images/home/hero.jpg') }}"
                     alt=""
                     width="1600" height="1200"
                     class="absolute inset-0 h-full w-full object-cover object-[center_35%]">
            </div>
        </div>
    </div>
</x-layouts.guest>
