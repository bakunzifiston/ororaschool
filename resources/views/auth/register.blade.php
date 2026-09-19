<x-layouts.guest title="Get your account">
    <x-auth-sheet :title="$page['title']" :subtitle="$page['subtitle']">

        {{-- Primary path. Most learners already hold an account on the platform
             they use daily, so linking comes first and self-registration second. --}}
        <form method="POST" action="{{ route('register.link') }}">
            @csrf

            <ul class="grid gap-2">
                @foreach ($page['platforms'] as $platform)
                    <li>
                        <button type="submit" name="platform" value="{{ $platform['slug'] }}"
                                class="flex w-full items-center gap-3 rounded-md border border-clay-200 bg-chalk px-3 py-2.5 text-left transition-colors hover:border-accent-500 hover:bg-accent-50">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-sm border border-clay-200 bg-papyrus text-dense font-semibold text-fern-600"
                                  aria-hidden="true">
                                {{ mb_substr($platform['name'], 0, 1) }}
                            </span>

                            <span class="min-w-0 grow">
                                <span class="block truncate text-dense font-medium text-basalt-900">
                                    Continue with {{ $platform['name'] }}
                                </span>
                                <span class="block truncate text-micro text-fern-500">{{ $platform['discipline'] }}</span>
                            </span>

                            <x-icon name="link" class="h-4 w-4 shrink-0 text-fern-400" />
                        </button>
                    </li>
                @endforeach
            </ul>
        </form>

        <p class="mt-3 text-micro leading-relaxed text-fern-500">{{ $page['linkNote'] }}</p>

        {{-- Ruled divider, in keeping with the record-sheet treatment. --}}
        <div class="my-6 flex items-center gap-3">
            <span class="h-px grow bg-clay-200"></span>
            <span class="shrink-0 text-micro text-fern-500">{{ $page['divider'] }}</span>
            <span class="h-px grow bg-clay-200"></span>
        </div>

        {{--
            SECONDARY PATH — self-registration.

            Whether Orora School allows learners to create their own account is
            not settled. If it does not, delete this form, the `register.store`
            route and its controller method: the linking list above stands on its
            own and the page still answers "how do I get access?".
        --}}
        <p class="mb-4 text-dense leading-relaxed text-fern-500">{{ $page['directIntro'] }}</p>

        <form method="POST" action="{{ route('register.store') }}" class="grid gap-5">
            @csrf

            <x-field name="name"
                     :label="$page['fields']['name']['label']"
                     :placeholder="$page['fields']['name']['placeholder']"
                     autocomplete="name" />

            <x-select name="district"
                      :label="$page['fields']['district']['label']"
                      :options="$page['districts']"
                      placeholder="Choose your district" />

            <x-field name="email" type="email"
                     :label="$page['fields']['email']['label']"
                     :placeholder="$page['fields']['email']['placeholder']"
                     :hint="$page['fields']['email']['hint']"
                     autocomplete="email" />

            <x-field name="password" type="password"
                     :label="$page['fields']['password']['label']"
                     :hint="$page['fields']['password']['hint']"
                     autocomplete="new-password"
                     revealable />

            <x-button type="submit" size="lg" class="w-full">{{ $page['submit'] }}</x-button>
        </form>

        <x-slot:footer>
            {{ $page['footer'] }}
            <a href="{{ route('login') }}" class="inline-block rounded-xs py-1.5 text-accent-600 underline-offset-2 hover:underline">
                {{ $page['footerLink'] }}
            </a>
        </x-slot:footer>
    </x-auth-sheet>
</x-layouts.guest>
