<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;

class UserStats extends StatsOverviewWidget
{
    // Ovaj widget će zauzimati pun width
    protected int|string|array $columnSpan = 'full';

    /** @var User */
    public User $record;

    // Filament će ovde ubaciti instancu korisnika sa ViewUser stranice
    public function mount(User $record): void
    {
        $this->record = $record;
    }

    protected function getCards(): array
    {
        return [
            Card::make('Preostala pitanja', $this->record->num_of_questions_left)
                ->description('Koliko pitanja korisnik još može da postavi')
                ->icon('heroicon-o-question-mark-circle'),

            Card::make('Ističe prava na pitanja', $this->record->questions_expires_at
                    ? $this->record->questions_expires_at->format('d.m.Y')
                    : '–')
                ->description('Datum isteka prava na nova pitanja')
                ->icon('heroicon-o-clock'),

            Card::make('Aktivni chatovi', $this->record->chats()->where('status', 'active')->count())
                ->description('Koliko chatova je trenutno otvoreno')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success'),

            Card::make('Zatvoreni chatovi', $this->record->chats()->where('status', 'closed')->count())
                ->description('Ukupan broj završenih chatova')
                ->icon('heroicon-o-chat-bubble-oval-left')
                ->color('secondary'),
        ];
    }
}
