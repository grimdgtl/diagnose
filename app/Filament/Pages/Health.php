<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\HealthCheck;
use App\Filament\Widgets\ErrorLogTable;

class Health extends Page
{
    protected static ?string $navigationLabel = 'Health';
    protected static ?int    $navigationSort  = 3;
    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static string  $view            = 'filament.pages.health';

    protected function getHeaderWidgets(): array
    {
        return [
            HealthCheck::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            ErrorLogTable::class,
        ];
    }
}
