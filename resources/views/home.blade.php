@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-neutral-200 bg-gradient-to-b from-brand-50/60 to-white px-8 py-16 text-center dark:border-neutral-800 dark:from-brand-950/30 dark:to-neutral-950 sm:px-16 sm:py-24">
        <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full bg-brand-400/20 blur-3xl"></div>

        <div class="relative">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-200 bg-white/70 px-3 py-1 text-xs font-medium text-brand-700 dark:border-brand-800 dark:bg-neutral-900/70 dark:text-brand-300">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                30-минутные звонки без переписки
            </span>

            <h1 class="mt-6 text-4xl font-semibold tracking-tight text-neutral-900 dark:text-white sm:text-5xl">
                Календарь звонков
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg text-neutral-600 dark:text-neutral-400">
                Запишитесь на 30-минутный звонок в удобное время — просто и без переписки.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('book.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-base font-medium text-white shadow-sm transition hover:bg-brand-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                    Записаться на звонок
                </a>
                <a href="{{ route('availabilities.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral-300 bg-white px-6 py-3 text-base font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                    Управление доступностью
                </a>
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold text-neutral-900 dark:text-white">30 минут</h2>
            <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-400">
                Фиксированная длительность звонка — без недопониманий по времени.
            </p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold text-neutral-900 dark:text-white">Выбор слота</h2>
            <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-400">
                Свободные 30-минутные интервалы в календаре организатора на выбор.
            </p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="m22 7-8.5 8.5-5-5L2 17"/>
                    <path d="M16 7h6v6"/>
                </svg>
            </div>
            <h2 class="mt-4 text-base font-semibold text-neutral-900 dark:text-white">Подтверждение на e-mail</h2>
            <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-400">
                Уведомление о записи приходит на почту сразу после выбора слота.
            </p>
        </div>
    </section>
@endsection
