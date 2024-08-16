<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FurnitureReservationResource\Pages;
use App\Filament\Resources\FurnitureReservationResource\RelationManagers;
use App\Models\FurnitureReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FurnitureReservationResource extends Resource
{
    protected static ?string $model = FurnitureReservation::class;

    protected static ?string $navigationIcon = 'bi-calendar2-check';

    protected static ?string $navigationGroup = 'Decor Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship(name: 'event', titleAttribute: 'title')
                        ->required(),
                    Forms\Components\Select::make('furniture_id')
                        ->relationship(name: 'furniture', titleAttribute: 'name')
                        ->label('Furniture')
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                    Forms\Components\DateTimePicker::make('start_date')
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('furniture.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
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
            ->groups([
                Group::make('furniture.name')
                    ->groupQueryUsing(fn (Builder $query) => $query->groupBy('furniture.name'))
            ])
            ->groups([
                Group::make('event.title')
                    ->groupQueryUsing(fn (Builder $query) => $query->groupBy('event.title'))
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
            'index' => Pages\ListFurnitureReservations::route('/'),
            'create' => Pages\CreateFurnitureReservation::route('/create'),
            'view' => Pages\ViewFurnitureReservation::route('/{record}'),
            'edit' => Pages\EditFurnitureReservation::route('/{record}/edit'),
        ];
    }
}
