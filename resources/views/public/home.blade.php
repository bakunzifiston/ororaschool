<x-layouts.public title="Training for the kraal, the plot and the collection centre" flush>
    {{-- Hero --}}
    <section class="public-hero-banner relative isolate overflow-hidden">
        <img src="{{ asset('images/home/hero.jpg') }}"
             alt="Farmers and a field officer reviewing a terraced plot in the Rwandan highlands."
             width="1600" height="1200"
             fetchpriority="high"
             class="absolute inset-0 h-full w-full object-cover object-[center_35%]">
        <div class="absolute inset-0 bg-gradient-to-r from-basalt-950/80 via-basalt-950/55 to-basalt-950/25"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-basalt-950/50 via-transparent to-basalt-950/20"></div>

        <div class="relative mx-auto flex min-h-[32rem] max-w-6xl items-end px-4 pt-16 pb-24 sm:min-h-[36rem] sm:px-6 sm:pt-20 sm:pb-28 lg:min-h-[40rem] lg:px-10 lg:pb-32">
            <div class="max-w-xl">
                <p class="public-eyebrow">FARMSCHOOL</p>
                <h1 class="marketing-hero mt-5 text-chalk">
                    Training that stays with the work.
                </h1>
                <p class="mt-5 max-w-md text-read leading-relaxed text-clay-200">
                    Practical training for the people, businesses and teams working across the Orora ecosystem.
                    Learn once, build real skills, and keep your learning record in one place.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <x-button size="lg" class="w-full sm:w-auto" :href="route('catalog.courses')">Explore courses</x-button>
                    <x-button size="lg" variant="secondary" class="w-full sm:w-auto" :href="route('register')">Get started</x-button>
                </div>
                <p class="mt-6 text-micro text-clay-200">
                    One account · Four academies · Verified certificates
                </p>
            </div>
        </div>
    </section>

    {{-- Statistics + platforms --}}
    <section class="relative z-10 bg-accent-50">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
            <dl class="public-card -mt-10 grid grid-cols-1 divide-y divide-clay-100 sm:-mt-12 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                @foreach ($page['stats'] as $stat)
                    <x-public.stat :value="$stat['value']" :label="$stat['label']" />
                @endforeach
            </dl>

            <div class="py-16 sm:py-20">
                <x-public.section-heading
                    title="Four academies. One school."
                    subtitle="One learning experience across the Orora ecosystem." />

                <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($page['platforms'] as $platform)
                        <x-public.platform-card :platform="$platform" />
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Featured courses --}}
    <x-public.section tone="chalk">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <x-public.section-heading
                title="Featured courses"
                subtitle="Practical training from across the Orora ecosystem." />
            <a href="{{ route('catalog.courses') }}"
               class="inline-flex shrink-0 items-center gap-1 text-dense font-medium text-accent-700 hover:underline">
                View all courses
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($page['featured'] as $course)
                <x-public.course-tile :course="$course" />
            @endforeach
        </div>
    </x-public.section>

    {{-- Value --}}
    <x-public.section tone="accent">
        <x-public.section-heading
            title="One account. Every learning journey."
            subtitle="Complete training across OroraFarm, Gemura, BuchaPro and FeedGrid while keeping your progress and certificates connected to one learning record." />

        <div class="mt-12 grid grid-cols-1 gap-10 sm:grid-cols-3 sm:gap-8">
            <x-public.feature icon="user" title="One account">
                Access learning across every connected academy without managing multiple accounts.
            </x-public.feature>
            <x-public.feature icon="path" title="Connected progress">
                Your courses and progress stay connected as you learn across the ecosystem.
            </x-public.feature>
            <x-public.feature icon="award" title="Verified certificates">
                Certificates can be independently verified using a public certificate number.
            </x-public.feature>
        </div>

        <div class="mt-12 flex flex-col gap-3 sm:flex-row">
            <x-button size="lg" class="w-full sm:w-auto" :href="route('register')">Create an account</x-button>
            <x-button size="lg" variant="secondary" class="w-full sm:w-auto" :href="route('login')">Sign in</x-button>
        </div>
    </x-public.section>

    {{-- Certificate verification --}}
    <x-public.section tone="mist">
        <div class="grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <h2 class="marketing-title text-basalt-900">Verify a FarmSchool certificate</h2>
                <p class="mt-3 max-w-md text-read leading-relaxed text-fern-600">
                    Check the authenticity of a FarmSchool certificate using its certificate number.
                </p>
            </div>

            <form method="GET" action="{{ route('certificates.lookup') }}" class="public-card p-6 sm:p-8">
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
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="marketing-title text-chalk">Build skills that work in the real world.</h2>
            <p class="mt-4 text-read leading-relaxed text-clay-200">
                Start learning with FarmSchool and build practical knowledge across the Orora ecosystem.
            </p>
            <div class="mt-9 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
                <x-button size="lg" class="w-full sm:w-auto" :href="route('catalog.courses')">Explore courses</x-button>
                <x-button size="lg" variant="secondary" class="w-full sm:w-auto" :href="route('register')">Create an account</x-button>
            </div>
        </div>
    </x-public.section>
</x-layouts.public>
