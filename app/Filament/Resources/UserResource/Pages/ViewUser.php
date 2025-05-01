<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Widgets\UserStats;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    // prikaži stats iznad relation-manager taba
    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }
}
