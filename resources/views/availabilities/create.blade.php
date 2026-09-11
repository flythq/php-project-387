@extends('layouts.app')

@section('title', 'Добавить окно доступности')

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

    <div class="mx-auto max-w-lg">
        <a href="{{ route('availabilities.index') }}" class="inline-flex items-center gap-1 text-sm text-neutral-500 transition hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="m12 19-7-7 7-7M19 12H5"/>
            </svg>
            Назад к окнам
        </a>

        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white">
            Добавить окно доступности
        </h1>
        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
            Задайте день недели и интервал времени, в который гости могут записываться.
        </p>

        <x-card class="mt-6 p-6">
            <form method="POST" action="{{ route('availabilities.store') }}" class="space-y-5">
                @csrf

                <x-input type="select" label="День недели" name="weekday">
                    @foreach ($weekdays as $value => $label)
                        <option value="{{ $value }}" {{ old('weekday') == (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </x-input>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input label="Начало (ЧЧ:ММ)" name="start_time" :value="old('start_time')" placeholder="10:00" />
                    <x-input label="Окончание (ЧЧ:ММ)" name="end_time" :value="old('end_time')" placeholder="18:00" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                        Сохранить
                    </button>
                    <a href="{{ route('availabilities.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral-300 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                        Отмена
                    </a>
                </div>
            </form>
        </x-card>
    </div>
@endsection
