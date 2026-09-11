<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Services\Scheduling\SlotGenerator;
use Carbon\CarbonImmutable;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function book(SlotGenerator $generator)
    {
        $now = CarbonImmutable::now(config('app.timezone'));

        $slots = $generator->generate(
            Availability::all(),
            $now,
            $now->addDays(config('booking.horizon_days', 14))->endOfDay(),
        );

        return view('book', [
            'days' => $slots->groupBy(fn ($slot) => $slot->start_at->format('Y-m-d')),
        ]);
    }
}
