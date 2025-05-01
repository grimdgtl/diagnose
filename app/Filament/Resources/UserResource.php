<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\CarDetailsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\ChatsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\QuestionsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Korisnici';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Ime')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Prezime')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel(),
                Forms\Components\TextInput::make('city')
                    ->label('Grad'),
                Forms\Components\TextInput::make('country')
                    ->label('Država'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('Ime')->searchable(),
                TextColumn::make('last_name')->label('Prezime')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->label('Telefon')->toggleable(),
                TextColumn::make('city')->label('Grad')->toggleable(),
                TextColumn::make('country')->label('Država')->toggleable(),
                TextColumn::make('num_of_questions_left')->label('Pitanja')->toggleable(),
                TextColumn::make('created_at')->label('Registrovan')->date('d.m.Y'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->label('Izmeni')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label('Obriši')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Obriši selektovane'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CarDetailsRelationManager::class,
            ChatsRelationManager::class,
            QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view'  => Pages\ViewUser::route('/{record}'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
