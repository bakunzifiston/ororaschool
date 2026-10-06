<x-layouts.public title="Training for the kraal, the plot and the collection centre" flush>
    <noscript>
        <style>[data-reveal]{opacity:1;transform:none}[data-reveal-media] img{transform:none}</style>
    </noscript>
    {{-- Hero --}}
    @php
        $published = $page['stats'][0] ?? ['value' => '', 'label' => ''];
        $companion = collect($page['platforms'])->firstWhere('slug', 'gemura') ?? [];
    @endphp
    <section class="bg-[#2a4d18]">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 sm:py-14 lg:grid-cols-2 lg:gap-16 lg:px-10 lg:py-16">
            <div class="max-w-xl is-in" data-reveal>
                <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
                    <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
                    FARMSCHOOL
                </p>
                <h1 class="marketing-hero mt-6 text-chalk">
                    Training that stays with the <em class="italic text-accent-200">work.</em>
                </h1>
                <p class="mt-5 max-w-md text-read leading-relaxed text-clay-200">
                    Practical training for the people, businesses and teams working in farming.
                    Learn once, build real skills, and keep your learning record in one place.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <x-button size="lg" class="w-full sm:w-auto" icon-after="arrow-right" :href="route('catalog.courses')">Explore courses</x-button>
                    <x-button size="lg" variant="secondary" class="w-full sm:w-auto" :href="route('register')">Get started</x-button>
                </div>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1.5" aria-hidden="true">
                        <span class="h-2 w-2 rounded-full bg-accent-500"></span>
                        <span class="h-2 w-2 rounded-full bg-accent-200"></span>
                        <span class="h-2 w-2 rounded-full bg-accent-400"></span>
                        <span class="h-2 w-2 rounded-full bg-chalk"></span>
                    </span>
                    <p class="text-micro text-clay-200">
                        One account · Four academies · Verified certificates
                    </p>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-md sm:max-w-lg lg:max-w-none">
                <div class="is-in overflow-hidden rounded-tl-[7rem] rounded-tr-3xl rounded-br-3xl rounded-bl-3xl" data-reveal data-reveal-media style="--reveal-delay: 120ms">
                    <img src="{{ asset('images/home/hero.jpg') }}"
                         alt="Farmers and a field officer reviewing a terraced plot in the Rwandan highlands."
                         width="1600" height="1200"
                         fetchpriority="high"
                         class="aspect-[5/6] w-full object-cover object-[center_35%]">
                </div>

                <p class="absolute left-1/2 top-5 z-10 inline-flex -translate-x-1/2 items-center gap-2 rounded-full bg-accent-100 px-3.5 py-1.5 text-micro font-medium text-basalt-800 is-in" data-reveal style="--reveal-delay: 220ms">
                    <span class="h-1.5 w-1.5 rounded-full bg-accent-500" aria-hidden="true"></span>
                    {{ $published['value'] }} {{ $published['label'] }}
                </p>

                <article class="absolute bottom-16 left-0 z-10 w-[62%] overflow-hidden rounded-2xl bg-chalk shadow-lg is-in sm:-left-8 lg:-left-12" data-reveal style="--reveal-delay: 280ms">
                    <img src="{{ asset('images/platforms/gemura.jpg') }}"
                         alt="Milk cans and a lactometer on a collection bench, with cattle behind the kraal fence."
                         width="1400" height="875"
                         class="aspect-[5/4] w-full object-cover object-[center_30%]">
                    <p class="flex items-center gap-2 px-4 py-3 text-micro font-medium text-basalt-800">
                        <span class="h-1.5 w-1.5 rounded-full bg-accent-500" aria-hidden="true"></span>
                        {{ $companion['name'] ?? 'Gemura' }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- Statistics + platforms --}}
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
            <dl class="grid grid-cols-1 divide-y divide-clay-200 py-12 sm:grid-cols-2 sm:divide-y-0 sm:py-14 lg:grid-cols-4 lg:divide-x">
                @foreach ($page['stats'] as $stat)
                    <x-public.stat :value="$stat['value']" :label="$stat['label']" :icon="$stat['icon'] ?? null" data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms" />
                @endforeach
            </dl>

            <div class="border-t border-clay-200 py-16 sm:py-20">
                <div data-reveal>
                    <x-public.section-heading
                        subtitle="One learning experience across farming.">
                        Four academies. <em class="italic text-accent-500">One school</em>.
                    </x-public.section-heading>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($page['platforms'] as $platform)
                        <x-public.platform-card :platform="$platform" data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms" />
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Value --}}
    <section class="bg-[#2a4d18]">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2 lg:gap-20 lg:px-10 lg:py-24">
            <div class="max-w-xl" data-reveal>
                <p class="flex items-center gap-3 text-micro font-medium tracking-[0.18em] text-clay-200">
                    <span class="h-px w-8 bg-accent-400" aria-hidden="true"></span>
                    FARMSCHOOL
                </p>
                <h2 class="marketing-hero mt-6 text-chalk">
                    One account. Every learning <em class="italic text-accent-200">journey.</em>
                </h2>
                <p class="mt-5 max-w-md text-read leading-relaxed text-clay-200">
                    Complete training across OroraFarm, Gemura, BuchaPro and FeedGrid while keeping your progress and certificates connected to one learning record.
                </p>
                <div class="mt-8 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                    <x-button size="lg" variant="secondary" class="w-full sm:w-auto" icon-after="arrow-right" :href="route('register')">Create an account</x-button>
                    <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center px-2 text-body font-medium text-chalk">Sign in</a>
                </div>
            </div>

            <ol class="grid">
                <li class="grid grid-cols-[auto_auto_minmax(0,1fr)] items-start gap-4 border-b border-chalk/10 py-6 first:pt-0" data-reveal style="--reveal-delay: 80ms">
                    <span class="figure pt-2 text-micro text-clay-200">01</span>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-basalt-800 text-accent-200">
                        <x-icon name="user" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="font-display text-panel font-semibold text-chalk">One account</h3>
                        <p class="mt-1.5 text-dense leading-relaxed text-clay-200">Access learning across every connected academy without managing multiple accounts.</p>
                    </div>
                </li>
                <li class="grid grid-cols-[auto_auto_minmax(0,1fr)] items-start gap-4 border-b border-chalk/10 py-6" data-reveal style="--reveal-delay: 160ms">
                    <span class="figure pt-2 text-micro text-clay-200">02</span>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-basalt-800 text-accent-200">
                        <x-icon name="file" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="font-display text-panel font-semibold text-chalk">Connected progress</h3>
                        <p class="mt-1.5 text-dense leading-relaxed text-clay-200">Your courses and progress stay connected as you learn across the ecosystem.</p>
                    </div>
                </li>
                <li class="grid grid-cols-[auto_auto_minmax(0,1fr)] items-start gap-4 py-6 last:pb-0" data-reveal style="--reveal-delay: 240ms">
                    <span class="figure pt-2 text-micro text-clay-200">03</span>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-basalt-800 text-accent-200">
                        <x-icon name="shield" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="font-display text-panel font-semibold text-chalk">Verified certificates</h3>
                        <p class="mt-1.5 text-dense leading-relaxed text-clay-200">Certificates can be independently verified using a public certificate number.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    {{-- Featured courses --}}
    <x-public.section tone="white">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal>
            <x-public.section-heading
                title="Featured courses"
                subtitle="Practical training from across farming." />
            <a href="{{ route('catalog.courses') }}"
               class="inline-flex shrink-0 items-center gap-1 text-dense font-medium text-accent-700 hover:underline">
                View all courses
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($page['featured'] as $course)
                <x-public.course-tile :course="$course" data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms" />
            @endforeach
        </div>
    </x-public.section>

    {{-- Certificate verification --}}
    <x-public.section tone="white">
        <div class="grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
            <div data-reveal>
                <h2 class="marketing-title text-basalt-900">Verify a FarmSchool certificate</h2>
                <p class="mt-3 max-w-md text-read leading-relaxed text-fern-600">
                    Check the authenticity of a FarmSchool certificate using its certificate number.
                </p>
            </div>

            <form method="GET" action="{{ route('certificates.lookup') }}" class="public-card p-6 sm:p-8" data-reveal style="--reveal-delay: 100ms">
                <x-field name="code" label="Certificate number"
                         placeholder="OS-GEM-2026-1847"
                         autocomplete="off" />
                <div class="mt-4">
                    <x-button type="submit" size="lg" class="w-full sm:w-auto">Verify certificate</x-button>
                </div>
            </form>
        </div>
    </x-public.section>

    {{-- Final CTA --}}
    <x-public.section tone="basalt" class="grow">
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <h2 class="marketing-title text-chalk">Build skills that work in the real world.</h2>
            <p class="mt-4 text-read leading-relaxed text-clay-200">
                Start learning with FarmSchool and build practical knowledge across farming.
            </p>
            <div class="mt-9 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
                <x-button size="lg" class="w-full sm:w-auto" :href="route('catalog.courses')">Explore courses</x-button>
                <x-button size="lg" variant="secondary" class="w-full sm:w-auto" :href="route('register')">Create an account</x-button>
            </div>
        </div>
    </x-public.section>
</x-layouts.public>
