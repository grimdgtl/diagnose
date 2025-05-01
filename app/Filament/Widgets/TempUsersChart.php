<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TempUser;

class TempUsersChart extends ChartWidget
{
    protected static ?string $heading = 'Privremene registracije (30d)';

    protected function getData(): array
    {
        $data = TempUser::query()
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Privremeni korisnici',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
