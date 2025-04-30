<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ChatsRelationManager extends RelationManager
{
    protected static string $relationship = 'chats';
    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Otvoren')->dateTime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('closed_at')->label('Zatvoren')->dateTime('d.m.Y H:i')->toggleable(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
