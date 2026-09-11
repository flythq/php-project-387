<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateBookingRequest;
use App\Http\Requests\Api\GetBookingRequest;

class BookingsController extends Controller
{
    public function store(CreateBookingRequest $request)
    {
        // TODO: implement — generated stub.
        return response()->json(new \stdClass, 201);
    }

    public function show(GetBookingRequest $request, string $id)
    {
        // TODO: implement — generated stub.
        return response()->json(['code' => 'not_found', 'message' => 'Resource not found'], 404);
    }
}
