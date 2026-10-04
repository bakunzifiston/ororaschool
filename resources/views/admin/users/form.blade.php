<x-layouts.super-admin :title="$page['header']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST"
          action="{{ $page['persisted']
              ? route('admin.accounts.update', $page['user']['id'])
              : ($page['isEdit'] ? route('admin.users.update', $page['user']['id']) : route('admin.users.store')) }}"
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

                @if ($page['persisted'] || ! $page['isEdit'])
                    <x-select name="role" label="Access" size="sm"
                              :options="$page['roles']"
                              :selected="$page['user']['role'] ?? 'platform-staff'"
                              hint="Academy staff land on the first assigned workspace. Super Admins can open every dashboard." />

                    <x-field name="password" type="password" label="{{ $page['persisted'] ? 'New password' : 'Password' }}" size="sm"
                             autocomplete="new-password" revealable :required="! $page['persisted']"
                             :hint="$page['persisted'] ? 'Leave blank to keep the current password.' : null" />

                    <x-field name="password_confirmation" type="password" label="Confirm password" size="sm"
                             autocomplete="new-password" revealable :required="! $page['persisted']" />
                @endif
            </div>
        </x-panel>

        @if ($page['persisted'] || ! $page['isEdit'])
            <x-panel class="mt-5" title="Academy dashboards"
                     subtitle="Tick every workspace this person may open. Leave empty only for Super Admins and learners.">
                @error('platforms')
                    <p class="mb-3 flex items-start gap-1.5 text-micro text-danger">
                        <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $message }}
                    </p>
                @enderror

                <fieldset class="grid gap-2">
                    <legend class="sr-only">Academy dashboards</legend>
                    @foreach ($page['platforms'] as $platform)
                        @php $id = 'platform-'.$platform['slug']; @endphp
                        <label for="{{ $id }}" class="flex items-start gap-3 rounded-md border border-clay-200 px-3.5 py-2.5 has-[:checked]:border-accent-400 has-[:checked]:bg-accent-50">
                            <input id="{{ $id }}" type="checkbox" name="platforms[]" value="{{ $platform['slug'] }}"
                                   class="mt-0.5 h-4 w-4 shrink-0 rounded-xs border-clay-300 text-accent-500"
                                   @checked(in_array($platform['slug'], old('platforms', $page['user']['platforms'] ?? []), true))>
                            <span class="min-w-0">
                                <span class="block text-dense font-medium text-basalt-900">{{ $platform['name'] }}</span>
                                <span class="block text-micro text-fern-500">{{ $platform['discipline'] }} · {{ $platform['status'] === 'active' ? 'Active' : 'Inactive' }}</span>
                            </span>
                        </label>
                    @endforeach
                </fieldset>
            </x-panel>
        @endif

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">{{ $page['isEdit'] ? 'Save user' : 'Create user' }}</x-button>
            <x-button variant="secondary" :href="route('admin.users')">Cancel</x-button>
        </div>
    </form>
</x-layouts.super-admin>
