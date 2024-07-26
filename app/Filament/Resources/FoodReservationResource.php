<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodReservationResource\Pages;
use App\Filament\Resources\FoodReservationResource\RelationManagers;
use App\Models\FoodReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FoodReservationResource extends Resource
{
    protected static ?string $model = FoodReservation::class;

    protected static ?string $navigationIcon = 'bi-calendar2-check';

    protected static ?string $navigationGroup = 'Catering Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('food_id')
                    ->relationship(name: 'food', titleAttribute: 'name')
                    ->required(),
                Forms\Components\Select::make('event_id')
                    ->relationship(name: 'event', titleAttribute: 'title')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('serving_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('food.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serving_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFoodReservations::route('/'),
            'create' => Pages\CreateFoodReservation::route('/create'),
            'view' => Pages\ViewFoodReservation::route('/{record}'),
            'edit' => Pages\EditFoodReservation::route('/{record}/edit'),
        ];
    }
}
