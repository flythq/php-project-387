<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['slot_start_at', 'invitee_name', 'invitee_email'];

    protected function casts(): array
    {
        return [
            'slot_start_at' => 'datetime',
        ];
    }
}
