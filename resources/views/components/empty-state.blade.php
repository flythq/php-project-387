@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-300 px-6 py-16 text-center dark:border-neutral-700']) }}>
    @if ($slot->isNotEmpty())
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-400">
            {{ $slot }}
        </div>
    @endif

    @if ($title)
        <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ $title }}</h3>
    @endif

    @isset($description)
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $description }}</p>
    @endisset
</div>
