@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
])

@php
    $fieldId = $name;
    $fieldValue = $value ?? old($name);
    $inputClasses = 'mt-1.5 block w-full rounded-xl border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition placeholder:text-neutral-400 focus:border-brand-500 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-brand-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder:text-neutral-500';
    $hasError = $errors->has($name);
    if ($hasError) {
        $inputClasses .= ' border-red-400 dark:border-red-500/60';
    }
@endphp

<div>
    @if ($label)
        <label for="{{ $fieldId }}" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $label }}</label>
    @endif

    @if ($type === 'select')
        <select name="{{ $name }}" id="{{ $fieldId }}" class="{{ $inputClasses }}">
            {{ $slot }}
        </select>
    @elseif ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $fieldId }}" class="{{ $inputClasses }}" placeholder="{{ $attributes->get('placeholder', '') }}">{{ $fieldValue }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $fieldId }}" value="{{ $fieldValue }}" placeholder="{{ $attributes->get('placeholder', '') }}" class="{{ $inputClasses }}">
    @endif

    @if ($hasError)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $errors->first($name) }}</p>
    @endif
</div>
