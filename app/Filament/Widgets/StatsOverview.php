<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Portfolio;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Services', Service::count())
                ->description('Active services offered')
                ->color('primary'),

            Stat::make('Portfolio Items', Portfolio::count())
                ->description('Published projects')
                ->color('success'),

            Stat::make('Pending Bookings', Booking::where('status', 'pending')->count())
                ->description('Awaiting confirmation')
                ->color('warning'),

            Stat::make('Unread Messages', ContactMessage::where('status', 'unread')->count())
                ->description('New contact messages')
                ->color('danger'),
        ];
    }
}