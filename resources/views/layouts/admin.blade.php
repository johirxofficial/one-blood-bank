@php
    $adminPrimary = (string) \App\Models\Setting::get('appearance.primary_color', '#D32F2F');
    $adminPrimary = preg_match('/^#[0-9a-fA-F]{6}$/', $adminPrimary) === 1 ? $adminPrimary : '#D32F2F';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="--color-brand-50: color-mix(in srgb, {{ $adminPrimary }} 8%, #f7f4ee); --color-brand-100: color-mix(in srgb, {{ $adminPrimary }} 18%, #f7f4ee); --color-brand-200: color-mix(in srgb, {{ $adminPrimary }} 32%, #f7f4ee); --color-brand-300: color-mix(in srgb, {{ $adminPrimary }} 48%, #f7f4ee); --color-brand-400: color-mix(in srgb, {{ $adminPrimary }} 66%, #f7f4ee); --color-brand-500: color-mix(in srgb, {{ $adminPrimary }} 82%, #f7f4ee); --color-brand-600: {{ $adminPrimary }}; --color-brand-700: color-mix(in srgb, {{ $adminPrimary }} 65%, #000); --color-brand-800: color-mix(in srgb, {{ $adminPrimary }} 55%, #000); --color-brand-900: color-mix(in srgb, {{ $adminPrimary }} 45%, #000);">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'অ্যাডমিন প্যানেল') — {{ \App\Models\Setting::get('site.name', 'One Blood Bank') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600&family=Tiro+Bangla:ital@0;1&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-paper">
    @php
        $navLink = fn (string $pattern): string => request()->routeIs($pattern)
            ? 'flex items-center gap-3 rounded-lg bg-paper/10 px-3 py-2.5 text-sm font-medium text-paper'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-normal text-paper/60 transition-colors hover:bg-paper/5 hover:text-paper';
        $pendingCount = \App\Models\Donor::query()->where('status', \App\Enums\DonorStatus::Pending)->count();
        $pendingSiteDonations = \App\Models\SiteDonation::query()->pending()->count();
    @endphp

    <div class="min-h-screen lg:flex">
        <div id="admin-backdrop" class="fixed inset-0 z-40 hidden bg-ink/50 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar --}}
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 hidden w-64 shrink-0 overflow-y-auto bg-ink px-4 py-5 lg:static lg:block">
            <div class="flex items-center justify-between gap-2 px-1">
                <x-logo :inverse="true" compact />
                <button type="button" data-menu-toggle="admin-sidebar" aria-label="মেনু বন্ধ করুন"
                        class="grid h-9 w-9 place-items-center rounded-lg text-paper/60 hover:bg-paper/10 hover:text-paper lg:hidden">
                    ✕
                </button>
            </div>

            <p class="mt-6 px-3 text-[11px] font-medium text-paper/40">প্রধান</p>
            <nav class="mt-2 grid gap-1">
                <a href="{{ route('admin.dashboard') }}" class="{{ $navLink('admin.dashboard') }}">
                    ড্যাশবোর্ড
                </a>
            </nav>

            <p class="mt-6 px-3 text-[11px] font-medium text-paper/40">ম্যানেজমেন্ট</p>
            <nav class="mt-2 grid gap-1">
                <a href="{{ route('admin.donors.index') }}" class="{{ $navLink('admin.donors.*') }}">
                    <span class="flex-1">ডোনার</span>
                    @if ($pendingCount > 0)
                        <span class="rounded-[5px] bg-paper/15 px-2 py-0.5 text-[11px] font-medium text-paper">{{ $pendingCount }}</span>
                    @endif
                </a>
                @if (auth()->user()->canManageUsers())
                    <a href="{{ route('admin.users.index') }}" class="{{ $navLink('admin.users.*') }}">
                        ইউজার
                    </a>
                @endif
                <a href="{{ route('admin.ratings.index') }}" class="{{ $navLink('admin.ratings.*') }}">
                    রেটিং ও রিভিউ
                </a>
                <a href="{{ route('admin.requests.index') }}" class="{{ $navLink('admin.requests.*') }}">
                    রক্তের অনুরোধ
                </a>
                <a href="{{ route('admin.messages.index') }}" class="{{ $navLink('admin.messages.*') }}">
                    বার্তা পাঠান
                </a>
            </nav>

            <p class="mt-6 px-3 text-[11px] font-medium text-paper/40">কনটেন্ট</p>
            <nav class="mt-2 grid gap-1">
                <a href="{{ route('admin.ads.index') }}" class="{{ $navLink('admin.ads.*') }}">
                    বিজ্ঞাপন
                </a>
                <a href="{{ route('admin.pages.index') }}" class="{{ $navLink('admin.pages.*') }}">
                    স্ট্যাটিক পেজ
                </a>
                <a href="{{ route('admin.blog.index') }}" class="{{ $navLink('admin.blog.*') }}">
                    ব্লগ
                </a>
                <a href="{{ route('admin.site-donations.index') }}" class="{{ $navLink('admin.site-donations.*') }}">
                    <span class="flex-1">সাইট ডোনেশন</span>
                    @if ($pendingSiteDonations > 0)
                        <span class="rounded-[5px] bg-paper/15 px-2 py-0.5 text-[11px] font-medium text-paper">{{ $pendingSiteDonations }}</span>
                    @endif
                </a>
            </nav>

            <p class="mt-6 px-3 text-[11px] font-medium text-paper/40">সিস্টেম</p>
            <nav class="mt-2 grid gap-1">
                @if (auth()->user()->canManageSettings())
                    <a href="{{ route('admin.settings.edit') }}" class="{{ $navLink('admin.settings.*') }}">
                        সেটিংস
                    </a>
                    <a href="{{ route('admin.locations.index') }}" class="{{ $navLink('admin.locations.*') }}">
                        লোকেশন ইমপোর্ট
                    </a>
                    <a href="{{ route('admin.staff.index') }}" class="{{ $navLink('admin.staff.*') }}">
                        স্টাফ ম্যানেজমেন্ট
                    </a>
                @endif
                <a href="{{ route('admin.logs.activity') }}" class="{{ $navLink('admin.logs.activity') }}">
                    অ্যাক্টিভিটি লগ
                </a>
                <a href="{{ route('admin.logs.mail') }}" class="{{ $navLink('admin.logs.mail') }}">
                    মেইল লগ
                </a>
            </nav>

            <div class="mt-8 rounded-xl border border-paper/10 bg-paper/5 p-3 text-xs text-paper/50">
                <p class="font-medium text-paper">{{ auth()->user()->name }}</p>
                <p class="mt-0.5">{{ auth()->user()->role->label() }} · @ {{ auth()->user()->username }}</p>
                <a href="{{ route('profile.edit') }}" class="mt-2 block text-xs font-normal text-paper/60 hover:text-paper">প্রোফাইল সেটিংস →</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-paper/10 px-3 py-2 text-xs font-medium text-paper hover:bg-paper/15">
                        লগআউট
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-line bg-surface/95 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                    <button type="button" data-menu-toggle="admin-sidebar" aria-label="মেনু"
                            class="grid h-9 w-9 place-items-center rounded-lg border border-line text-ink-soft lg:hidden">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
                    </button>

                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-ink-muted">অ্যাডমিন প্যানেল</p>
                        <h1 class="truncate text-sm font-medium text-ink">@yield('heading', 'ড্যাশবোর্ড')</h1>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('home') }}" target="_blank" rel="noopener"
                           class="btn-secondary btn-sm hidden sm:inline-flex">
                            ↗ সাইট দেখুন
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-secondary btn-sm hidden md:inline-flex">
                            আমার ড্যাশবোর্ড
                        </a>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <x-alerts />
                @yield('content')
            </main>

            <footer class="border-t border-line bg-surface px-4 py-4 text-center text-xs text-ink-muted sm:px-6">
                © {{ now()->year }} {{ \App\Models\Setting::get('site.name', 'One Blood Bank') }} — অ্যাডমিন প্যানেল
            </footer>
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-backdrop');
            if (!sidebar) return;

            const sync = () => {
                if (backdrop) backdrop.classList.toggle('hidden', sidebar.classList.contains('hidden'));
            };

            document.querySelectorAll('[data-menu-toggle="admin-sidebar"]').forEach((button) => {
                button.addEventListener('click', () => {
                    setTimeout(sync, 0);
                });
            });

            backdrop?.addEventListener('click', () => {
                sidebar.classList.add('hidden');
                sync();
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
