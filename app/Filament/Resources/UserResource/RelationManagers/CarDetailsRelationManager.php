<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CarDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'carDetails';
    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand')
                    ->label('Proizvođač')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('model')
                    ->label('Model')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('year')
                    ->label('Godište')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('engine_power')
                    ->label('Snaga vozila (KS)')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('engine_capacity')
                    ->label('Zapremina motora (cc)')
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('fuel_type')
                    ->label('Vrsta goriva')
                    ->options([
                        'gasoline'  => 'Benzin',
                        'diesel'    => 'Dizel',
                        'electric'  => 'Električno',
                        'hybrid'    => 'Hibridno',
                    ])
                    ->required(),

                Forms\Components\Select::make('transmission')
                    ->label('Tip menjača')
                    ->options([
                        'manual'     => 'Ručni',
                        'automatic'  => 'Automatski',
                        'semiauto'   => 'Poluautomatski',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // vidljive po difoltu
                Tables\Columns\TextColumn::make('brand')
                    ->label('Proizvođač')
                    ->sortable(),

                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Godište')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dodato')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // opciono kolone (skrivene po difoltu, mogu se uključiti u UI)
                Tables\Columns\TextColumn::make('engine_power')
                    ->label('Snaga vozila (KS)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('engine_capacity')
                    ->label('Zapremina motora (cc)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fuel_type')
                    ->label('Vrsta goriva')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('transmission')
                    ->label('Tip menjača')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
