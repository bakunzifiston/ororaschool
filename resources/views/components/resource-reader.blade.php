@props(['resource', 'chrome' => true])

@php
    $viewer = $resource['viewer'] ?? 'file';
    $fileUrl = $resource['file_url'] ?? null;
    $downloadUrl = $resource['download_url'] ?? null;
    $hasFile = filled($fileUrl);
    $extension = $resource['extension'] ?? '';
    $embedPdf = $hasFile && (
        $viewer === 'pdf'
        || ($viewer === 'document' && ! in_array($extension, ['doc', 'docx'], true))
    );
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-md border border-clay-200 bg-chalk']) }}>
    @if ($chrome)
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-clay-200 px-5 py-4 sm:px-6">
            <div class="min-w-0">
                <p class="text-micro font-medium text-fern-500">{{ $resource['type_label'] }}</p>
                <h1 class="mt-1 font-display text-section text-basalt-900">{{ $resource['title'] }}</h1>
                <p class="mt-1 text-micro text-fern-500">
                    {{ $resource['platform_name'] }}
                    · {{ $resource['attached_to'] }}
                    @if (($resource['size'] ?? '') !== '')
                        · {{ $resource['size'] }}
                    @endif
                </p>
            </div>

            @if ($hasFile && $downloadUrl)
                <x-button variant="secondary" size="sm" icon="arrow-down" :href="$downloadUrl">Download</x-button>
            @endif
        </div>
    @endif

    @if (($viewer === 'youtube') && filled($resource['youtube_embed'] ?? null))
        <div class="bg-basalt-900">
            <iframe src="{{ $resource['youtube_embed'] }}"
                    title="{{ $resource['title'] }}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    class="aspect-video mx-auto w-full max-w-4xl border-0"></iframe>
        </div>
        @if (filled($resource['youtube_watch'] ?? null))
            <div class="flex justify-end border-t border-clay-200 px-5 py-3">
                <x-button variant="ghost" size="sm" :href="$resource['youtube_watch']" target="_blank" rel="noopener noreferrer">Open on YouTube</x-button>
            </div>
        @endif
    @elseif ($embedPdf)
        <div class="bg-basalt-800 px-3 py-6 sm:px-8 sm:py-10">
            <iframe src="{{ $fileUrl }}#toolbar=1&navpanes=0&view=FitH"
                    title="{{ $resource['title'] }}"
                    type="application/pdf"
                    class="resource-pdf-frame mx-auto w-full max-w-4xl border-0 bg-chalk"></iframe>
        </div>
    @elseif ($viewer === 'pdf' || $viewer === 'document')
        <div class="bg-papyrus px-3 py-6 sm:px-8 sm:py-10">
            <article class="resource-paper mx-auto max-w-[40rem] px-8 py-12 sm:px-14 sm:py-16">
                <p class="text-micro font-medium tracking-wide text-fern-500">Document</p>
                <h2 class="mt-8 font-display text-title text-basalt-900">{{ $resource['title'] }}</h2>
                <p class="mt-4 text-read leading-relaxed text-fern-600">{{ $resource['attached_to'] }}</p>
                <hr class="my-8 border-clay-200">
                <dl class="grid gap-4 text-dense sm:grid-cols-2">
                    <div>
                        <dt class="text-micro text-fern-500">Academy</dt>
                        <dd class="mt-1 text-basalt-900">{{ $resource['platform_name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-micro text-fern-500">Updated</dt>
                        <dd class="mt-1 text-basalt-900">{{ $resource['updated'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-micro text-fern-500">Size</dt>
                        <dd class="mt-1 text-basalt-900">{{ $resource['size'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-micro text-fern-500">Type</dt>
                        <dd class="mt-1 text-basalt-900">{{ $resource['type_label'] }}</dd>
                    </div>
                </dl>
                @if ($hasFile && $downloadUrl)
                    <div class="mt-10">
                        <x-button icon="arrow-down" :href="$downloadUrl">Download document</x-button>
                    </div>
                @endif
            </article>
        </div>
    @elseif ($viewer === 'image' && $hasFile)
        <div class="bg-papyrus px-4 py-8 sm:px-8">
            <img src="{{ $fileUrl }}" alt="{{ $resource['title'] }}" class="mx-auto max-h-[70vh] w-auto max-w-full">
        </div>
    @elseif ($viewer === 'video' && $hasFile)
        <div class="bg-basalt-900">
            <video controls src="{{ $fileUrl }}" class="mx-auto max-h-[70vh] w-full max-w-4xl"></video>
        </div>
    @else
        <div class="px-5 py-10 text-center sm:px-8">
            <x-icon name="file" class="mx-auto h-8 w-8 text-fern-500" />
            <p class="mt-3 font-display text-panel font-semibold text-basalt-900">{{ $resource['title'] }}</p>
            <p class="mt-1 text-dense text-fern-500">{{ $resource['type_label'] }} · {{ $resource['size'] }} · {{ $resource['updated'] }}</p>
            @if ($hasFile && $downloadUrl)
                <div class="mt-5">
                    <x-button icon="arrow-down" :href="$downloadUrl">Download</x-button>
                </div>
            @endif
        </div>
    @endif
</div>
