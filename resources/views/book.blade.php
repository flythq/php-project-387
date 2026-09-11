@extends('layouts.app')

@section('title', 'Запись на звонок')

@section('content')
    @php
        $weekdaysShort = [
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб',
            7 => 'Вс',
        ];
        $weekdaysFull = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
    @endphp

    <div class="mx-auto max-w-5xl">
        <a href="{{ route('home.index') }}" class="inline-flex items-center gap-1 text-sm text-neutral-500 transition hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="m12 19-7-7 7-7M19 12H5"/>
            </svg>
            На главную
        </a>

        <div class="mt-4">
            <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                Запись на звонок
            </h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                Выберите день и свободный 30-минутный слот, затем оставьте свои данные.
            </p>
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

        @if ($days->isEmpty())
            <x-empty-state class="mt-8" title="Свободных слотов пока нет" description="Загляните позже — организатор добавит новые окна доступности.">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </x-empty-state>
        @else
            <form method="POST" action="{{ route('book.store') }}" class="mt-8 space-y-8" data-book-form>
                @csrf

                <div class="grid gap-6 lg:grid-cols-[17rem_1fr]">
                    <x-card class="p-3" data-day-picker>
                        <h2 class="px-2 pb-2 pt-1 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                            Дни
                        </h2>
                        <ul class="flex gap-2 overflow-x-auto p-1 lg:flex-col lg:overflow-visible" data-day-list>
                            @foreach ($days as $dateKey => $daySlots)
                                @php
                                    $day = \Carbon\CarbonImmutable::createFromFormat('Y-m-d', $dateKey);
                                    $freeCount = $daySlots->filter(fn ($s) => $s->is_available)->count();
                                @endphp
                                <li class="shrink-0 lg:shrink">
                                    <button type="button" data-day="{{ $dateKey }}" @class([
                                        'flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition lg:w-full',
                                        'border border-transparent hover:bg-neutral-50 dark:hover:bg-neutral-800/60',
                                        'aria-selected:border-brand-500 aria-selected:bg-brand-50 aria-selected:text-brand-700 dark:aria-selected:bg-brand-950/50 dark:aria-selected:text-brand-300',
                                        $freeCount === 0 ? 'opacity-50' : '',
                                    ]) aria-selected="false">
                                        <span class="flex h-10 w-10 flex-col items-center justify-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                                            <span class="text-[10px] font-medium uppercase tracking-wide">{{ $weekdaysShort[$day->format('N')] }}</span>
                                            <span class="text-sm font-bold leading-none">{{ $day->format('j') }}</span>
                                        </span>
                                        <span class="flex flex-col">
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $weekdaysFull[$day->format('N')] }}</span>
                                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $day->format('d.m.Y') }} · {{ $freeCount }} св.</span>
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </x-card>

                    <div class="space-y-6" data-day-panels>
                        @foreach ($days as $dateKey => $daySlots)
                            @php
                                $day = \Carbon\CarbonImmutable::createFromFormat('Y-m-d', $dateKey);
                                $freeCount = $daySlots->filter(fn ($s) => $s->is_available)->count();
                            @endphp
                            <div data-day-panel="{{ $dateKey }}">
                                <x-card class="p-6">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">
                                                {{ $weekdaysFull[$day->format('N')] }}
                                            </h3>
                                            <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">
                                                {{ $day->format('d.m.Y') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                                            {{ $freeCount }} свободно
                                        </span>
                                    </div>

                                    @if ($daySlots->isEmpty())
                                        <p class="mt-6 text-sm text-neutral-500 dark:text-neutral-400">
                                            Свободных слотов на этот день нет.
                                        </p>
                                    @else
                                        <ul class="mt-5 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                                            @foreach ($daySlots as $slot)
                                                <li>
                                                    @if ($slot->is_available)
                                                        <label class="group flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-neutral-300 px-4 py-3 transition hover:border-brand-400 hover:bg-brand-50/40 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-500/30 dark:border-neutral-700 dark:hover:border-brand-600 dark:hover:bg-brand-950/30 dark:has-[:checked]:border-brand-500 dark:has-[:checked]:bg-brand-950/50">
                                                            <input type="radio" name="slot_start_at" value="{{ $slot->start_at->toDateTimeString() }}" class="sr-only">
                                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $slot->start_at->format('H:i') }}–{{ $slot->end_at->format('H:i') }}</span>
                                                        </label>
                                                    @else
                                                        <div class="flex cursor-not-allowed items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-400 dark:border-neutral-800 dark:bg-neutral-900/60 dark:text-neutral-600" aria-disabled="true" data-disabled-slot>
                                                            <span class="text-sm font-medium line-through">{{ $slot->start_at->format('H:i') }}–{{ $slot->end_at->format('H:i') }}</span>
                                                        </div>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </x-card>
                            </div>
                        @endforeach
                    </div>
                </div>

                @error('slot_start_at')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <x-card class="p-6">
                    <h2 class="text-sm font-semibold text-neutral-900 dark:text-white">Ваши данные</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-input label="Имя" name="invitee_name" :value="old('invitee_name')" />
                        <x-input label="E-mail" name="invitee_email" type="email" :value="old('invitee_email')" />
                    </div>
                </x-card>

                <div class="flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-base font-medium text-white shadow-sm transition hover:bg-brand-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                        Записаться
                    </button>
                </div>
            </form>

            <script>
                (function () {
                    var panels = document.querySelectorAll('[data-day-panel]');
                    var buttons = document.querySelectorAll('[data-day]');
                    if (panels.length === 0) return;

                    panels.forEach(function (panel) { panel.classList.add('hidden'); });
                    showPanel(panels[0].getAttribute('data-day-panel'));

                    buttons.forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            showPanel(btn.getAttribute('data-day'));
                        });
                    });

                    function showPanel(dateKey) {
                        panels.forEach(function (panel) {
                            panel.classList.toggle('hidden', panel.getAttribute('data-day-panel') !== dateKey);
                        });
                        buttons.forEach(function (btn) {
                            var selected = btn.getAttribute('data-day') === dateKey;
                            btn.setAttribute('aria-selected', selected ? 'true' : 'false');
                        });
                        var active = document.querySelector('[data-day="' + dateKey + '"]');
                        if (active) active.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                })();
            </script>
        @endif
    </div>
@endsection
