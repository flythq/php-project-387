@extends('layouts.app')

@section('title', 'Запись подтверждена')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-card class="p-8 text-center sm:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>

            <h1 class="mt-6 text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                Запись подтверждена
            </h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">
                Ваша запись на звонок зафиксирована. Подтверждение отправлено на e-mail.
            </p>

            <dl class="mt-8 space-y-3 border-t border-neutral-200 pt-6 text-left dark:border-neutral-800">
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-neutral-500 dark:text-neutral-400">Когда</dt>
                    <dd class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $booking->slot_start_at->format('d.m.Y H:i') }} <span class="text-neutral-400">(30 минут)</span></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-neutral-500 dark:text-neutral-400">Имя</dt>
                    <dd class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $booking->invitee_name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-sm text-neutral-500 dark:text-neutral-400">E-mail</dt>
                    <dd class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $booking->invitee_email }}</dd>
                </div>
            </dl>

            <div class="mt-8">
                <a href="{{ route('home.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral-300 bg-white px-6 py-3 text-base font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                    На главную
                </a>
            </div>
        </x-card>
    </div>
@endsection
