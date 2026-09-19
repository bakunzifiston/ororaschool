@props(['selected' => null])

@php
    $options = \App\Support\DemoData\Pages\AuthPages::previewOptions();
    $selected = $selected ?? \App\Support\DemoData\Preview::experience();
@endphp

{{--
    ======================================================================
    TEMPORARY DEVELOPMENT CONTROL — REMOVE WITH REAL AUTHENTICATION.

    There are no credentials to check yet, so submitting the sign-in form
    just opens the shell named here. Deleting this whole `components/dev`
    folder, App\Support\DemoData\Preview and the preview_as lines in
    AuthPagesController removes the behaviour completely.

    Deliberately styled as a warning strip rather than a form field: it must
    never be mistaken for part of the real interface, and it must be
    obvious in a screenshot that this build has no authentication.
    ======================================================================
--}}
<div class="mt-6 rounded-md border border-dashed border-st-pending/60 bg-st-pending-bg/50 p-4">
    <p class="flex items-center gap-1.5 text-micro font-semibold text-st-pending">
        <x-icon name="alert" class="h-3.5 w-3.5" />
        Temporary — no authentication in this build
    </p>

    <p class="mt-1.5 text-micro leading-relaxed text-fern-600">
        Nothing above is checked. Choose which shell to open, then sign in. This strip disappears
        when the backend adds real accounts.
    </p>

    <label for="preview-as" class="mt-3 block text-micro font-medium text-basalt-800">
        Open the shell for
    </label>

    <select id="preview-as" name="preview_as"
            class="mt-1 h-10 w-full rounded-md border border-clay-300 bg-chalk px-2.5 text-dense text-basalt-800">
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" @selected($key === $selected)>
                {{ $option['label'] }} — {{ $option['detail'] }}
            </option>
        @endforeach
    </select>
</div>
