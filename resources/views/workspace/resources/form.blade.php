<x-layouts.platform-workspace :title="$page['header']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST"
          action="{{ route('workspace.resources.store', ['platform' => $platformSlug]) }}"
          enctype="multipart/form-data"
          class="mt-6 max-w-2xl"
          x-data="{
              type: @js(old('type', '')),
              kind: @js(old('attached_kind', '')),
              target: @js(old('attached_key', '')),
              fileName: '',
              accepts: @js($page['accepts']),
              hints: @js($page['hints']),
              targets: @js($page['targets']),
              labels: { academy: 'Focus', course: 'Course', module: 'Module', lesson: 'Lesson' },
          }"
          x-on:change="if ($event.target.name === 'attached_kind') { target = '' }">
        @csrf

        <x-panel title="File">
            <div class="grid gap-5">
                <x-field name="title" label="Title" size="sm" placeholder="e.g. CMT field sheet" />

                <x-select name="type" label="Type" size="sm"
                          placeholder="Choose a type"
                          required
                          model="type"
                          :options="$page['types']" />
                <p class="text-micro text-fern-500" x-text="type ? (hints[type] || '') : 'Choose a type. That choice sets the label and the files we accept.'"></p>

                <x-select name="attached_kind" label="Attach to" size="sm"
                          placeholder="Choose where it sits"
                          required
                          model="kind"
                          :options="$page['kinds']" />

                <div x-show="kind !== ''" x-cloak>
                    <label for="field-attached_key" class="mb-1.5 block text-dense font-medium text-basalt-800">
                        <span x-text="labels[kind] || 'Target'"></span>
                    </label>
                    <select id="field-attached_key"
                            name="attached_key"
                            x-model="target"
                            required
                            class="h-9 w-full rounded-md border px-2.5 text-dense text-basalt-800 {{ $errors->has('attached_key') ? 'border-danger' : 'border-clay-300' }} bg-chalk">
                        <template x-for="(label, value) in (targets[kind] || { '': 'Choose' })" :key="String(value)">
                            <option :value="value" x-text="label" :selected="String(value) === String(target)"></option>
                        </template>
                    </select>
                    @error('attached_key')
                        <p class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
                            <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $message }}
                        </p>
                    @enderror
                </div>

                <p class="text-micro text-fern-500"
                   x-text="kind === 'academy'
                       ? 'Open on the academy page and the public Resources list. Learners do not need to enrol.'
                       : (kind === '' ? 'Academy files are open. Course, module and lesson files wait until a learner enrols.' : 'Learners only see this after they enrol on that course.')"></p>

                <div>
                    <p class="mb-1.5 text-dense font-medium text-basalt-800">File</p>
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border border-clay-200 bg-chalk px-4 py-4">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-clay-200 text-fern-500">
                            <x-icon name="upload" class="h-5 w-5" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-dense text-basalt-800" x-text="fileName || 'Choose a file'"></span>
                            <span class="mt-0.5 block text-micro text-fern-500"
                                  x-text="type ? (hints[type] + ' Optional in this build.') : 'Choose a type first.'"></span>
                        </span>
                        <input type="file"
                               name="file"
                               class="sr-only"
                               aria-label="Resource file"
                               :accept="accepts[type] || ''"
                               x-on:change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                    </label>
                    @error('file')
                        <p class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
                            <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">Save resource</x-button>
            <x-button variant="secondary" :href="route('workspace.resources', ['platform' => $platformSlug])">Cancel</x-button>
        </div>
    </form>
</x-layouts.platform-workspace>
