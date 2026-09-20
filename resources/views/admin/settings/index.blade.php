<x-layouts.super-admin title="Settings">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 max-w-2xl">
        @csrf

        <x-panel title="Certificates" subtitle="The certificate numbering format minted when a learner finishes.">
            <x-select name="certificate_format" label="Certificate numbering format" size="sm"
                      :options="array_keys($page['formats'])"
                      :selected="$page['values']['certificate_format']"
                      hint="Tokens: {PLATFORM} short code, {YEAR}, {SEQ:n} zero-padded sequence. Example for Gemura: {{ $page['formats'][$page['values']['certificate_format']] }}." />
        </x-panel>

        <x-panel class="mt-4" title="Lists" subtitle="Default page size for estate tables. Workspaces can override.">
            <x-select name="pagination" label="Default pagination size" size="sm"
                      :options="$page['paginationSizes']"
                      :selected="$page['values']['pagination']" />
        </x-panel>

        <x-panel class="mt-4" title="Support contact" subtitle="Shown on learner certificates and the help footer.">
            <div class="grid gap-5">
                <x-field name="support_name" label="Desk name" size="sm" :value="$page['values']['support_name']" />
                <x-field name="support_email" type="email" label="Email" size="sm" :value="$page['values']['support_email']" />
                <x-field name="support_phone" label="Phone" size="sm" :value="$page['values']['support_phone']"
                         hint="Field officers often call. Keep a number that is staffed during clinic hours." />
            </div>
        </x-panel>

        <x-panel class="mt-4" title="Session">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="session_timeout" label="Idle timeout (minutes)" size="sm"
                         :value="$page['values']['session_timeout']" />
                <x-select name="locale" label="Default language" size="sm"
                          :options="$page['locales']"
                          :selected="$page['values']['locale']" />
            </div>
        </x-panel>

        <div class="mt-4">
            <x-button type="submit">Save settings</x-button>
        </div>
    </form>
</x-layouts.super-admin>
