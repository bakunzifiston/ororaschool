@props(['experience' => 'super-admin'])

@php
    // The proof page only renders inside the three app layouts, so guest pages
    // link into the Super Admin one.
    $proofExperience = in_array($experience, ['super-admin', 'platform-workspace', 'learner'], true)
        ? $experience
        : 'super-admin';
@endphp

<footer class="mt-10 border-t border-clay-200 px-4 py-5 sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 text-micro text-fern-500">
        <p>Orora School — training and certification for Rwandan farmers and field officers.</p>
        <p>
            Demonstration build · every record on screen is fixture data ·
            <a href="{{ route('design.components', ['experience' => $proofExperience]) }}"
               class="rounded-xs underline underline-offset-2 hover:text-basalt-800">component proof</a>
        </p>
    </div>
</footer>
