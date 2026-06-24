<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function updateStatus(Booking $booking, string $status, ?string $notes = null): Booking
    {
        $booking->update([
            'status' => $status,
            'admin_notes' => $notes ?? $booking->admin_notes,
        ]);

        return $booking;
    }

    public function getPendingCount(): int
    {
        return Booking::pending()->count();
    }
}