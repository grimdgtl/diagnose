<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Rating;

class HealthCheck extends StatsOverviewWidget
{
    protected ?string $heading = 'Health Check';
    protected int|string|array $columnSpan = 'full';

    protected function getCards(): array
    {
        // proveravamo cache; ako nema ili je isteklo, ponovo pingujemo
        $gptOk = Cache::remember('openai_ok', now()->addHour(), function () {
            try {
                $key = config('services.openai.key');
                return Http::withToken($key)
                            ->timeout(6)
                            ->get('https://api.openai.com/v1/models')
                            ->ok();
            } catch (\Throwable $e) {
                return false;
            }
        });

        $dbOk = rescue(fn () => DB::connection()->getPdo(), false) ? true : false;
        $avg  = number_format(Rating::avg('rating') ?? 0, 2);
        $cnt  = Rating::count();

        return [
            Card::make('GPT API', $gptOk ? 'OK' : 'FAIL')
                ->color($gptOk ? 'success' : 'danger')
                ->icon($gptOk ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

            Card::make('DB', $dbOk ? 'Connected' : 'Error')
                ->color($dbOk ? 'success' : 'danger')
                ->icon($dbOk ? 'heroicon-o-server-stack' : 'heroicon-o-exclamation-circle'),

            Card::make('Prosečna ocena', "$avg ($cnt)")
                ->color('warning')
                ->icon('heroicon-o-star'),
        ];
    }
}
