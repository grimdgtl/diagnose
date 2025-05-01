<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions;
use Filament\Tables\Filters\Filter;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;
    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Ocene';
    protected static ?int    $navigationSort  = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('rating')
                    ->label('Ocena')
                    ->required()
                    ->minValue(1)
                    ->maxValue(5),
                Textarea::make('feedback')
                    ->label('Komentar')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.first_name')->label('Ime')->sortable(),
                TextColumn::make('user.last_name')->label('Prezime')->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('rating')->label('Ocena')->sortable(),
                TextColumn::make('feedback')->label('Komentar')->limit(50)->wrap(),
                TextColumn::make('created_at')->label('Datum')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRatings::route('/'),
            'view'   => Pages\ViewRating::route('/{record}'),
            'create' => Pages\CreateRating::route('/create'),
            'edit'   => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
