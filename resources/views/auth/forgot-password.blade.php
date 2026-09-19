<x-layouts.guest title="Reset your password">
    <x-auth-sheet :title="$page['title']" :step="$page['step']" :subtitle="$page['subtitle']">
        <form method="POST" action="{{ route('password.email') }}" class="grid gap-5">
            @csrf

            <x-field name="email" type="email"
                     :label="$page['email']['label']"
                     :placeholder="$page['email']['placeholder']"
                     autocomplete="username"
                     autofocus />

            <x-button type="submit" size="lg" icon="mail" class="w-full">{{ $page['submit'] }}</x-button>
        </form>

        <div class="mt-5 border-t border-clay-100 pt-4">
            <p class="text-micro leading-relaxed text-fern-500">{{ $page['aside'] }}</p>
        </div>

        <x-slot:footer>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 rounded-xs py-1.5 text-accent-600 underline-offset-2 hover:underline">
                <x-icon name="chevron-left" class="h-3 w-3" />{{ $page['back'] }}
            </a>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
