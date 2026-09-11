<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name', 'Календарь звонков'))</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen font-sans antialiased">
        <div class="relative flex min-h-screen flex-col">
            <header class="sticky top-0 z-40 border-b border-neutral-200/80 bg-white/80 backdrop-blur-md dark:border-neutral-800/80 dark:bg-neutral-950/80">
                <div class="mx-auto flex h-16 w-full max-w-5xl items-center justify-between px-6">
                    <a href="{{ route('home.index') }}" class="flex items-center gap-2 font-semibold tracking-tight text-neutral-900 dark:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </span>
                        Календарь звонков
                    </a>
                    <nav class="flex items-center gap-1">
                        <a href="{{ route('book.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100">
                            Записаться
                        </a>
                        <a href="{{ route('availabilities.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100">
                            Доступность
                        </a>
                        <a href="{{ route('bookings.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100">
                            Записи
                        </a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-12">
                @yield('content')
            </main>

            <footer class="mx-auto w-full max-w-5xl px-6 py-8">
                <p class="text-sm text-neutral-400 dark:text-neutral-600">
                    v{{ app()->version() }}
                </p>
            </footer>
        </div>
    </body>
</html>
