<x-layouts.guest title="Set a new password">
    <x-auth-sheet :title="$page['title']" :step="$page['step']" :subtitle="$page['subtitle']">
        <form method="POST" action="{{ route('password.update') }}" class="grid gap-5">
            @csrf
            <input type="hidden" name="token" value="{{ $page['token'] }}">

            {{-- Shown, not hidden: people need to see which account they are
                 changing when a link arrives days later. --}}
            <x-field name="email" type="email" label="Account" :value="$page['email']" readonly />

            <x-field name="password" type="password"
                     :label="$page['fields']['password']['label']"
                     :hint="$page['fields']['password']['hint']"
                     autocomplete="new-password"
                     revealable
                     autofocus />

            <x-field name="password_confirmation" type="password"
                     :label="$page['fields']['confirmation']['label']"
                     :hint="$page['fields']['confirmation']['hint']"
                     autocomplete="new-password"
                     revealable />

            <x-button type="submit" size="lg" class="w-full">{{ $page['submit'] }}</x-button>
        </form>

        <x-slot:footer>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 rounded-xs py-1.5 text-accent-600 underline-offset-2 hover:underline">
                <x-icon name="chevron-left" class="h-3 w-3" />{{ $page['back'] }}
            </a>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
