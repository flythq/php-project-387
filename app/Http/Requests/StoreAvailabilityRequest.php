<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => ['required', 'date_format:H:i', function ($attribute, $value, $fail) {
                if ($value <= $this->input('start_time')) {
                    $fail('Время окончания должно быть позже времени начала.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'weekday.required' => 'День недели обязателен.',
            'weekday.integer' => 'День недели должен быть числом.',
            'weekday.min' => 'День недели — от 1 (Пн) до 7 (Вс).',
            'weekday.max' => 'День недели — от 1 (Пн) до 7 (Вс).',
            'start_time.required' => 'Время начала обязательно.',
            'start_time.date_format' => 'Время начала должно быть в формате ЧЧ:ММ.',
            'end_time.required' => 'Время окончания обязательно.',
            'end_time.date_format' => 'Время окончания должно быть в формате ЧЧ:ММ.',
        ];
    }
}
