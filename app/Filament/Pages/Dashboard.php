<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

// naše widget-e
use App\Filament\Widgets\HealthCheck;
use App\Filament\Widgets\TempUsersChart;
use App\Filament\Widgets\UsersChart;
use App\Filament\Widgets\PackagesChart;
use App\Filament\Widgets\ErrorLogTable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int    $navigationSort  = 1;

    //
    // 1. red — HealthCheck (API status, DB status, prosečna ocena)
    //
    public function getHeaderWidgets(): array
    {
        return [
            HealthCheck::class,
        ];
    }

    //
    // 2. red — temp korisnici & registrovani korisnici
    //
    public function getWidgets(): array
    {
        return [
            TempUsersChart::class,
            UsersChart::class,
        ];
    }

    //
    // 3. red — prodati paketi & poslednjih 5 grešaka
    //
    public function getFooterWidgets(): array
    {
        return [
            PackagesChart::class,
            ErrorLogTable::class,
        ];
    }
}
