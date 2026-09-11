<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Models\Availability;

class AvailabilitiesController extends Controller
{
    public function index()
    {
        $availabilities = Availability::orderBy('weekday')->orderBy('start_time')->get();

        return view('availabilities.index', ['availabilities' => $availabilities]);
    }

    public function create()
    {
        return view('availabilities.create');
    }

    public function store(StoreAvailabilityRequest $request)
    {
        Availability::create($request->validated());

        return redirect()->route('availabilities.index')->with('status', 'Окно доступности добавлено.');
    }

    public function edit(Availability $availability)
    {
        return view('availabilities.edit', ['availability' => $availability]);
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability)
    {
        $availability->update($request->validated());

        return redirect()->route('availabilities.index')->with('status', 'Окно доступности обновлено.');
    }

    public function destroy(Availability $availability)
    {
        $availability->delete();

        return redirect()->route('availabilities.index')->with('status', 'Окно доступности удалено.');
    }
}
