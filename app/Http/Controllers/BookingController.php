<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingCreatedMail;
use App\Models\Booking;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = Booking::create($request->validated());
        } catch (UniqueConstraintViolationException $e) {
            return redirect()
                ->route('book.index')
                ->withInput()
                ->withErrors(['slot_start_at' => 'Слот уже занят.']);
        }

        Mail::to($booking->invitee_email)->send(new BookingCreatedMail($booking, forHost: false));
        Mail::to(config('booking.host_email'))->send(new BookingCreatedMail($booking, forHost: true));

        return redirect()->route('book.success', $booking)->with('status', 'Запись создана.');
    }

    public function success(Booking $booking)
    {
        return view('book.success', ['booking' => $booking]);
    }
}
