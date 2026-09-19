<x-layouts.guest title="Sign in">
    <x-auth-sheet :title="$page['title']" :subtitle="$page['subtitle']">
        <form method="POST" action="{{ route('login.attempt') }}" class="grid gap-5">
            @csrf

            <x-field name="email" type="email"
                     :label="$page['email']['label']"
                     :placeholder="$page['email']['placeholder']"
                     autocomplete="username"
                     autofocus />

            <x-field name="password" type="password"
                     :label="$page['password']['label']"
                     autocomplete="current-password"
                     revealable>
                <x-slot:labelEnd>
                    <a href="{{ route('password.request') }}"
                       class="inline-block rounded-xs py-1 text-accent-600 underline-offset-2 hover:underline">
                        {{ $page['forgot'] }}
                    </a>
                </x-slot:labelEnd>
            </x-field>

            <x-checkbox name="remember" :label="$page['remember']" :hint="$page['rememberHint']" />

            {{-- TEMPORARY: decides where this form lands. Remove with real auth. --}}
            <x-dev.preview-as />

            <x-button type="submit" size="lg" class="w-full">{{ $page['submit'] }}</x-button>
        </form>

        <x-slot:footer>
            {{ $page['footer'] }}
            <a href="{{ route('register') }}" class="inline-block rounded-xs py-1.5 text-accent-600 underline-offset-2 hover:underline">
                {{ $page['footerLink'] }}
            </a>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
