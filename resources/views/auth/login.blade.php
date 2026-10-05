<x-layouts.guest title="Sign in" wide>
    <div class="overflow-hidden rounded-3xl border border-clay-200 bg-chalk shadow-lg">
        <div class="grid lg:grid-cols-2">
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-12 lg:py-14">
                <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-fern-500">
                    <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
                    FARMSCHOOL
                </p>

                <h1 class="mt-5 font-display text-title tracking-tight text-basalt-900">{{ $page['title'] }}</h1>
                <p class="mt-3 text-dense leading-relaxed text-fern-600">{{ $page['subtitle'] }}</p>

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 grid gap-5">
                    @csrf

                    <x-field name="email" type="email" appearance="pill"
                             :label="$page['email']['label']"
                             :placeholder="$page['email']['placeholder']"
                             autocomplete="username"
                             autofocus />

                    <x-field name="password" type="password" appearance="pill"
                             :label="$page['password']['label']"
                             autocomplete="current-password"
                             revealable />

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <x-checkbox name="remember" :label="$page['remember']" />
                        <a href="{{ route('password.request') }}"
                           class="text-micro font-medium text-accent-600 underline-offset-2 hover:underline">
                            {{ $page['forgot'] }}
                        </a>
                    </div>

                    <x-button type="submit" size="lg" variant="forest" class="w-full">
                        {{ $page['submit'] }}
                    </x-button>
                </form>

                <p class="mt-8 text-center text-dense text-fern-500">
                    {{ $page['footer'] }}
                    <a href="{{ route('register') }}" class="font-medium text-accent-600 underline-offset-2 hover:underline">
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
