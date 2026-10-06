<x-layouts.super-admin title="Profile">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-8 max-w-2xl">
        @csrf

        <x-panel title="Your details">
            <div class="grid gap-5">
                <x-field name="name" label="Full name" size="sm"
                         :value="$page['user']['name']" required />

                <x-field name="email" type="email" label="Email address" size="sm"
                         :value="$page['user']['email']" required />

                <x-select name="district" label="District" size="sm"
                          :options="$page['districts']"
                          :selected="$page['user']['district']"
                          placeholder="Choose a district" required />

                <x-field name="password" type="password" label="New password" size="sm"
                         autocomplete="new-password" revealable
                         hint="Leave blank to keep your current password." />

                <x-field name="password_confirmation" type="password" label="Confirm password" size="sm"
                         autocomplete="new-password" revealable />
            </div>
        </x-panel>

        <div class="mt-4">
            <x-button type="submit">Save profile</x-button>
        </div>
    </form>
</x-layouts.super-admin>
