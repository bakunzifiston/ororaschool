<x-layouts.guest title="Confirm your email address">
    <x-auth-sheet :title="$page['title']" :subtitle="$page['subtitle']">
        <ul class="grid gap-2.5">
            @foreach ($page['reasons'] as $reason)
                <li class="flex items-start gap-2.5 text-dense leading-relaxed text-fern-600">
                    <x-icon name="check" class="mt-1 h-3.5 w-3.5 shrink-0 text-accent-500" />
                    {{ $reason }}
                </li>
            @endforeach
        </ul>

        <div class="mt-5 border-t border-clay-100 pt-4">
            <p class="text-micro leading-relaxed text-fern-500">{{ $page['aside'] }}</p>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                @csrf
                <x-button type="submit" variant="secondary" size="lg" icon="mail" class="w-full">
                    {{ $page['resend'] }}
                </x-button>
            </form>
        </div>

        <x-slot:footer>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="text-micro">{{ $page['wrongAddress'] }}</span>

                <form method="POST" action="{{ route('sign-out') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xs py-1.5 text-micro text-accent-600 underline-offset-2 hover:underline">
                        <x-icon name="log-out" class="h-3 w-3" />Sign out
                    </button>
                </form>
            </div>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
