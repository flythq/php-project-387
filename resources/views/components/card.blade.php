@props([
    'as' => 'div',
])

<{{ $as }} {{ $attributes->merge(['class' => 'rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900']) }}>
    {{ $slot }}
</{{ $as }}>
