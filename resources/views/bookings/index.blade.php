@extends('layouts.app')

@section('title', 'Список записей')

@section('content')
    <div>
        <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white">
            Записи на звонки
        </h1>
        <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-400">
            Все записи гостей на 30-минутные звонки, отсортированные по времени.
        </p>
    </div>

    @if ($bookings->isEmpty())
        <x-empty-state class="mt-8" title="Записей нет" description="Как только гости начнут записываться, они появятся здесь.">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </x-empty-state>
    @else
        <div class="mt-8 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-800">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50/80 dark:border-neutral-800 dark:bg-neutral-900/80">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Когда</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Гость</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">E-mail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($bookings as $booking)
                        <tr class="transition hover:bg-neutral-50/60 dark:hover:bg-neutral-800/40">
                            <td class="px-5 py-4 text-neutral-900 dark:text-neutral-100">
                                <span class="font-medium">{{ $booking->slot_start_at->format('d.m.Y H:i') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                                        {{ mb_strtoupper(mb_substr($booking->invitee_name, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $booking->invitee_name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-neutral-600 dark:text-neutral-400">{{ $booking->invitee_email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
