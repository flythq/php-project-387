@extends('layouts.app')

@section('title', 'Окна доступности')

@section('content')
    @php
        $weekdays = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
    @endphp

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                Окна доступности
            </h1>
            <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-400">
                Еженедельные окна, в которые гости могут записаться на звонок.
            </p>
        </div>
        <a href="{{ route('availabilities.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="M5 12h14M12 5v14"/>
            </svg>
            Добавить окно
        </a>
    </div>

    @if (session('status'))
        <div class="mt-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 flex-shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="m9 11 3 3L22 4"/>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($availabilities->isEmpty())
        <x-empty-state class="mt-8" title="Окна доступности не заданы" description="Добавьте первое окно, чтобы гости могли записываться.">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <rect width="18" height="18" x="3" y="4" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18M12 14h.01M12 18h.01"/>
            </svg>
        </x-empty-state>
    @else
        <div class="mt-8 grid gap-3 sm:grid-cols-2">
            @foreach ($availabilities as $availability)
                <x-card class="flex items-center justify-between p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-11 w-11 flex-shrink-0 flex-col items-center justify-center rounded-xl bg-brand-50 text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                            <span class="text-[10px] font-medium uppercase tracking-wide">{{ mb_substr($weekdays[$availability->weekday] ?? '', 0, 3) }}</span>
                            <span class="text-sm font-bold leading-none">{{ $availability->weekday }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $weekdays[$availability->weekday] ?? "День {$availability->weekday}" }}</p>
                            <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $availability->start_time?->format('H:i') }} — {{ $availability->end_time?->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('availabilities.edit', $availability) }}" class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100">
                            Изменить
                        </a>
                        <form method="POST" action="{{ route('availabilities.destroy', $availability) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:text-red-400 dark:hover:bg-red-950/40">
                                Удалить
                            </button>
                        </form>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
