<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ErrorLogTable extends BaseWidget
{
    protected static ?string $heading = 'Poslednjih 5 grešaka';

    // I ovaj widget neka zauzme pola širine
    

    protected function getTableQuery(): Builder
    {
        // prazan model samo da dobijemo Eloquent builder
        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $table = 'failed_jobs';
            public $timestamps = false;
        };

        return $model->newQuery()
            ->orderByDesc('failed_at')
            ->limit(5)
            ->select(['id', 'failed_at', 'exception']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('failed_at')
                ->label('Vreme')
                ->dateTime('d.m.Y H:i'),

            Tables\Columns\TextColumn::make('exception')
                ->label('Exception')
                ->limit(80)
                ->tooltip(fn ($state) => $state),
        ];
    }
}
