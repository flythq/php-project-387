<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invitee.email' => 'required|email',
            'invitee.name' => 'required|string',
            'slotId' => 'required|uuid',
        ];
    }
}
