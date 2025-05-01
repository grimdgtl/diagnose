<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;

class PackagesChart extends ChartWidget
{
    protected static ?string $heading = 'Prodati paketi (30d)';

    // Ovim widget zauzima pola širine (6 od 12 kolona)
    

    protected function getData(): array
    {
        $data = Order::query()
            ->whereDate('ordered_at', '>=', now()->subDays(30))
            ->selectRaw('variant_id, COUNT(*) as total')
            ->groupBy('variant_id')
            ->pluck('total', 'variant_id')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Broj porudžbina',
                    'data'  => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
