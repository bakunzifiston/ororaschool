<x-layouts.public title="Training for the kraal, the plot and the collection centre" flush>
    {{-- Hero --}}
    <section class="border-b border-clay-200 bg-papyrus">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-2 lg:gap-14 lg:px-10 lg:py-20">
            <div>
                <p class="text-micro font-medium tracking-wider text-accent-700">ORORA SCHOOL</p>
                <h1 class="marketing-hero mt-3 max-w-xl text-basalt-900">
                    Training that stays with the work.
                </h1>
                <p class="mt-4 max-w-md text-read leading-relaxed text-fern-600">
                    Learn across OroraFarm, Gemura, BuchaPro and FeedGrid with one account
                    and one place for your certificates.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-button size="lg" :href="route('catalog.courses')">Explore courses</x-button>
                    <x-button size="lg" variant="secondary" :href="route('register')">Get started</x-button>
                </div>
                <dl class="mt-8 grid grid-cols-3 gap-4 max-w-lg">
                    <div>
                        <dt class="sr-only">Published courses</dt>
                        <dd class="figure text-section font-semibold text-basalt-900">{{ $page['stats']['courses'] }}</dd>
                        <p class="mt-1 text-micro text-fern-500">Published courses</p>
                    </div>
                    <div>
                        <dt class="sr-only">Platforms</dt>
                        <dd class="figure text-section font-semibold text-basalt-900">{{ $page['stats']['platforms'] }}</dd>
                        <p class="mt-1 text-micro text-fern-500">Platforms</p>
                    </div>
                    <div>
                        <dt class="sr-only">Learning record</dt>
                        <dd class="text-section font-semibold text-basalt-900">One</dd>
                        <p class="mt-1 text-micro text-fern-500">Learning record</p>
                    </div>
                </dl>
            </div>

            <div class="relative mx-auto w-full max-w-md lg:mx-0 lg:max-w-none">
                <div class="public-card p-4">
                    <p class="text-micro font-medium text-fern-500">Course progress</p>
                    <p class="mt-1 font-display text-panel font-semibold text-basalt-900">Mastitis Detection</p>
                    <p class="mt-1 text-micro text-fern-500">Gemura · 12 of 18 lessons</p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-clay-100">
                        <div class="h-full w-2/3 rounded-full bg-accent-500"></div>
                    </div>
                </div>
                <div class="public-card mt-3 p-4">
                    <p class="text-micro font-medium text-fern-500">Certificate</p>
                    <p class="mt-1 font-display text-panel font-semibold text-basalt-900">Verified on Gemura</p>
                    <p class="mt-1 font-mono text-micro text-fern-500">OS-GEM-2026-1847</p>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($page['platforms'] as $platform)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-clay-200 bg-chalk px-2.5 py-1.5 text-micro text-basalt-800">
                            {{ $platform['name'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Platforms --}}
    <section class="bg-chalk">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-10 lg:py-16">
            <h2 class="font-display text-title text-basalt-900">Four platforms. One school.</h2>
            <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">
                Learn the skills behind the Orora ecosystem without managing separate learning accounts.
            </p>
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($page['platforms'] as $platform)
                    <x-public.platform-card :platform="$platform" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured courses --}}
    <section class="border-y border-clay-200 bg-papyrus">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-10 lg:py-16">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="font-display text-title text-basalt-900">Featured courses</h2>
                    <p class="mt-3 max-w-xl text-read leading-relaxed text-fern-600">
                        Practical training from across the Orora ecosystem.
                    </p>
                </div>
                <a href="{{ route('catalog.courses') }}" class="inline-flex items-center gap-1 text-dense font-medium text-accent-700 hover:underline">
                    View all courses
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($page['featured'] as $course)
                    <x-public.course-tile :course="$course" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Value --}}
    <section class="marketing-band-basalt on-basalt bg-basalt-900 text-chalk">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-10 lg:py-16">
            <h2 class="font-display text-title text-chalk">One account. Every learning journey.</h2>
            <p class="mt-3 max-w-xl text-read leading-relaxed text-clay-200">
                Complete training across OroraFarm, Gemura, BuchaPro and FeedGrid.
                Progress and certificates stay attached to the same person.
            </p>
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <p class="font-display text-panel font-semibold text-chalk">One account</p>
                    <p class="mt-2 text-dense leading-relaxed text-clay-200">Access learning on every live platform without a second login.</p>
                </div>
                <div>
                    <p class="font-display text-panel font-semibold text-chalk">Connected progress</p>
                    <p class="mt-2 text-dense leading-relaxed text-clay-200">Courses you start on one platform remain on your record when you train on another.</p>
                </div>
                <div>
                    <p class="font-display text-panel font-semibold text-chalk">Verified certificates</p>
                    <p class="mt-2 text-dense leading-relaxed text-clay-200">Minted numbers can be checked from a public page — no sign-in required.</p>
                </div>
            </div>
            <div class="mt-10 flex flex-wrap gap-3">
                <x-button size="lg" :href="route('login')">Sign in</x-button>
                <x-button size="lg" variant="secondary" :href="route('register')">Create an account</x-button>
            </div>
        </div>
    </section>

    {{-- Certificate verification --}}
    <section class="bg-chalk">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-10 lg:py-16">
            <div class="public-card mx-auto max-w-xl p-6 sm:p-8">
                <h2 class="font-display text-section text-basalt-900">Verify a certificate</h2>
                <p class="mt-2 text-dense leading-relaxed text-fern-600">Check whether an Orora School certificate is valid.</p>
                <form method="GET" action="{{ route('certificates.lookup') }}" class="mt-6">
                    <x-field name="code" label="Certificate number" size="sm"
                             placeholder="OS-GEM-2026-1847"
                             autocomplete="off" />
                    <div class="mt-4">
                        <x-button type="submit">Verify certificate</x-button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
