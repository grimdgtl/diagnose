<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Rating;

class RatingsCard extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $avg = number_format(Rating::avg('rating') ?? 0, 2);
        $cnt = Rating::count();

        return [
            Card::make('Prosečna ocena', "$avg ( $cnt )")
                ->color('yellow')
                ->icon('heroicon-o-star'),
        ];
    }
}
