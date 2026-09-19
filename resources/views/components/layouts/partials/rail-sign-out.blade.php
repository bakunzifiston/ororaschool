{{-- Sign-out inside the drawer, for viewports where the top bar is not shown. --}}
<form method="POST" action="{{ route('sign-out') }}" class="mt-3 lg:hidden">
    @csrf
    <button type="submit"
            class="flex w-full items-center gap-2 rounded-sm border border-basalt-700 px-2 py-1.5 text-dense text-clay-200 transition-colors hover:bg-basalt-800 hover:text-white">
        <x-icon name="log-out" class="h-4 w-4 text-fern-400" />
        Sign out
    </button>
</form>
