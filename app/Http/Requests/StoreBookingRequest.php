<?php

namespace App\Http\Requests;

use App\Models\Availability;
use App\Services\Scheduling\Slot;
use App\Services\Scheduling\SlotGenerator;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_start_at' => ['required', 'date', function (string $attribute, mixed $value, \Closure $fail) {
                $start = Carbon::parse($value, config('app.timezone'));
                $now = CarbonImmutable::now(config('app.timezone'));

                if ($start < $now) {
                    $fail('Слот в прошлом.');

                    return;
                }

                $horizonEnd = $now->addDays((int) config('booking.horizon_days', 14))->endOfDay();

                if ($start > $horizonEnd) {
                    $fail('Слот вне горизонта записи.');

                    return;
                }

                $available = app(SlotGenerator::class)
                    ->generate(Availability::all(), $now, $horizonEnd)
                    ->contains(fn (Slot $slot) => $slot->is_available && $slot->start_at->equalTo($start));

                if (! $available) {
                    $fail('Слот недоступен для записи.');
                }
            }],
            'invitee_name' => 'required|string|max:255',
            'invitee_email' => 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'slot_start_at.required' => 'Выберите слот.',
            'slot_start_at.date' => 'Слот должен быть датой.',
            'invitee_name.required' => 'Имя обязательно.',
            'invitee_name.max' => 'Имя не должно быть длиннее 255 символов.',
            'invitee_email.required' => 'E-mail обязателен.',
            'invitee_email.email' => 'Введите корректный e-mail.',
        ];
    }
}
