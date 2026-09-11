<?php

use App\Http\Controllers\AvailabilitiesController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/book', [HomeController::class, 'book'])->name('book.index');
Route::post('/book', [BookingController::class, 'store'])->name('book.store');
Route::get('/book/success/{booking}', [BookingController::class, 'success'])->name('book.success');

Route::get('/bookings', [BookingsController::class, 'index'])->name('bookings.index');

Route::resource('availabilities', AvailabilitiesController::class);
