<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DecorationItemReservationResource\Pages;
use App\Filament\Resources\DecorationItemReservationResource\RelationManagers;
use App\Models\DecorationItemReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DecorationItemReservationResource extends Resource
{
    protected static ?string $model = DecorationItemReservation::class;

    protected static ?string $navigationIcon = 'bi-calendar2-check';

    protected static ?string $navigationGroup = 'Decor Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship(name: 'event', titleAttribute: 'title')
                        ->required(),
                    Forms\Components\Select::make('decoration_item_id')
                        ->relationship(name: 'decorationItem', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\DateTimePicker::make('start_date')
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
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
                Tables\Columns\TextColumn::make('decorationitem.name')
                    ->label('Decoration Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
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
                Group::make('decorationitem.name')
                    ->groupQueryUsing(fn(Builder $query) => $query->groupBy('decorationitem.name'))
                    ->label('Decoration Item')
            ])
            ->groups([
                Group::make('event.title')
                    ->groupQueryUsing(fn(Builder $query) => $query->groupBy('event.title'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDecorationItemReservations::route('/'),
//            'create' => Pages\CreateDecorationItemReservation::route('/create'),
            'view' => Pages\ViewDecorationItemReservation::route('/{record}'),
//            'edit' => Pages\EditDecorationItemReservation::route('/{record}/edit'),
        ];
    }
}
