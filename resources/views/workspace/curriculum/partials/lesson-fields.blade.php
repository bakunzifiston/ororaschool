@php
    $types = $types ?? [];
    $lesson = $lesson ?? null;
    $selectedType = $selectedType ?? 'video';
    $fieldSuffix = $fieldSuffix ?? 'new';
    $type = old('type', $lesson['type'] ?? $selectedType);
    $title = old('title', $lesson['title'] ?? '');
    $duration = old('duration', $lesson['duration'] ?? 8);
    $sourceUrl = old('source_url', $lesson['source_url'] ?? '');
    $body = old('body', $lesson['body'] ?? '');
    $suffix = $lesson['id'] ?? $fieldSuffix;
    $hasFile = (bool) ($lesson['has_file'] ?? false);
    $pdfHint = \App\Support\DemoData\Resources::fileHints()['pdf'];
@endphp

<div class="grid gap-4"
     x-data="{
         type: @js($type),
         labels: @js($types),
         videoHint: @js(\App\Support\DemoData\Resources::fileHints()['video']),
         pdfHint: @js($pdfHint),
         fileName: '',
         fileSuffix: @js($hasFile ? ' Leave blank to keep the current file.' : ''),
     }">
    <x-field name="title" label="Lesson name" size="sm" :value="$title" required />

    <x-select name="type" label="Content type" size="sm"
              model="type"
              :options="$types"
              :selected="$type"
              required />

    <div>
        <label for="field-duration-{{ $suffix }}" class="mb-1.5 block text-dense font-medium text-basalt-800">Duration (minutes)</label>
        <input id="field-duration-{{ $suffix }}"
               type="number"
               name="duration"
               value="{{ $duration }}"
               min="1"
               max="600"
               required
               class="h-9 w-full rounded-md border border-clay-300 bg-chalk px-2.5 text-dense text-basalt-800">
    </div>

    <div x-show="['video', 'external', 'pdf', 'audio'].includes(type)" x-cloak>
        <label for="field-source_url-{{ $suffix }}" class="mb-1.5 block text-dense font-medium text-basalt-800"
               x-text="type === 'video' ? 'YouTube link' : (labels[type] || '')">YouTube link</label>
        <input id="field-source_url-{{ $suffix }}"
               type="url"
               name="source_url"
               value="{{ $sourceUrl }}"
               x-bind:placeholder="type === 'video' ? 'https://youtu.be/…' : 'https://'"
               x-bind:disabled="!['video', 'external', 'pdf', 'audio'].includes(type)"
               class="h-9 w-full rounded-md border bg-chalk px-2.5 text-dense text-basalt-800 {{ $errors->has('source_url') ? 'border-danger' : 'border-clay-300' }}">
        <p class="mt-1.5 text-micro leading-relaxed text-fern-500"
           x-show="type === 'video'"
           x-cloak
           x-text="videoHint"></p>
        @error('source_url')
            <p class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
                <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $message }}
            </p>
        @enderror
    </div>

    <div x-show="type === 'pdf'" x-cloak>
        <p class="mb-1.5 text-dense font-medium text-basalt-800">File</p>
        <label class="flex cursor-pointer items-center gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-4">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-clay-200 text-fern-500">
                <x-icon name="upload" class="h-5 w-5" />
            </span>
            <span class="min-w-0">
                <span class="block text-dense text-basalt-800" x-text="fileName || 'Choose a file'"></span>
                <span class="mt-0.5 block text-micro text-fern-500"
                      x-text="pdfHint + fileSuffix"></span>
            </span>
            <input type="file"
                   name="file"
                   accept=".pdf,application/pdf"
                   class="sr-only"
                   aria-label="Lesson file"
                   x-bind:disabled="type !== 'pdf'"
                   x-on:change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
        </label>
        @error('file')
            <p class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
                <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $message }}
            </p>
        @enderror
    </div>

    <div x-show="['text', 'video', 'audio'].includes(type)" x-cloak>
        <label for="field-body-{{ $suffix }}" class="mb-1.5 block text-dense font-medium text-basalt-800">Description</label>
        <textarea id="field-body-{{ $suffix }}"
                  name="body"
                  rows="4"
                  x-bind:disabled="!['text', 'video', 'audio'].includes(type)"
                  class="w-full rounded-md border border-clay-300 bg-chalk px-3 py-2 text-dense text-basalt-800 placeholder:text-clay-300">{{ $body }}</textarea>
    </div>

    <p class="text-micro leading-relaxed text-fern-500"
       x-show="type === 'live_session'"
       x-cloak>
        The join link will appear here when a clinic is scheduled.
    </p>
</div>
