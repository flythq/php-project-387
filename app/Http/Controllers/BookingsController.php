<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::orderBy('slot_start_at')->get();

        return view('bookings.index', ['bookings' => $bookings]);
    }
}
