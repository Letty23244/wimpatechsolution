<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Service;

class BookingController extends Controller
{
    public function index()
    {
        $services = Service::active()->orderBy('sort_order')->get();

        return view('public.booking', compact('services'));
    }

    public function store(BookingRequest $request)
    {
        Booking::create($request->validated());

        return back()->with('success', 'Your consultation has been booked! We will confirm your appointment shortly.');
    }
}