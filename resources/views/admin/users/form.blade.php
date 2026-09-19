<x-layouts.super-admin :title="$page['header']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST"
          action="{{ $page['isEdit'] ? route('admin.users.update', $page['user']['id']) : route('admin.users.store') }}"
          class="mt-6 max-w-2xl">
        @csrf

        <x-panel title="Account">
            <div class="grid gap-5">
                <x-field name="name" label="Full name" size="sm"
                         :value="$page['user']['name']"
                         placeholder="e.g. Josiane Kayitesi" />

                <x-field name="email" type="email" label="Email address" size="sm"
                         :value="$page['user']['email']"
                         placeholder="name@gemura.rw" />

                <x-select name="district" label="District" size="sm"
                          :options="$page['districts']"
                          :selected="$page['user']['district']"
                          placeholder="Choose a district" />

                <x-select name="status" label="Account status" size="sm"
                          :options="$page['statuses']"
                          :selected="$page['user']['status']" />
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">{{ $page['isEdit'] ? 'Save user' : 'Send invite' }}</x-button>
            <x-button variant="secondary" :href="route('admin.users')">Cancel</x-button>
        </div>
    </form>
</x-layouts.super-admin>
