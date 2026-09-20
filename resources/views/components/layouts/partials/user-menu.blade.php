@props(['user' => [], 'role' => null, 'compact' => false])

{{-- Current user plus the sign-out control. Sign-out posts to a placeholder
     route that flashes a message — there is no session to end yet. --}}
<div class="flex items-center gap-3">
    @unless ($compact)
        <span class="hidden text-micro text-fern-500 sm:inline">{{ $user['district'] ?? '' }}</span>
        @if ($role)
            <x-role-chip :role="$role" class="hidden sm:inline-flex" />
        @endif
    @endunless

    <span class="flex items-center gap-2">
        <x-avatar :name="$user['name'] ?? ''" size="sm" />
        <span class="hidden min-w-0 md:block">
            <span class="block truncate text-micro font-medium text-basalt-800">{{ $user['name'] ?? '' }}</span>
            <span class="block truncate text-micro text-fern-500">{{ $user['title'] ?? '' }}</span>
        </span>
    </span>

    <form method="POST" action="{{ route('sign-out') }}" class="shrink-0">
        @csrf
        <button type="submit"
                class="inline-flex h-8 items-center gap-1.5 rounded-md border border-clay-200 bg-chalk px-2.5 text-micro text-fern-500 transition-colors hover:border-fern-400 hover:text-basalt-900"
                aria-label="Sign out">
            <x-icon name="log-out" class="h-3.5 w-3.5" />
            <span class="hidden sm:inline">Sign out</span>
        </button>
    </form>
</div>
