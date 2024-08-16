<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoundReservationResource\Pages;
use App\Filament\Resources\SoundReservationResource\RelationManagers;
use App\Models\SoundReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoundReservationResource extends Resource
{
    protected static ?string $model = SoundReservation::class;

    protected static ?string $navigationIcon = 'bi-calendar2-check';

    protected static ?string $navigationGroup = 'Sound Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship(name: 'event', titleAttribute: 'title')
                        ->required(),
                    Forms\Components\Select::make('sound_id')
                        ->relationship(name: 'sound', titleAttribute: 'artist')
                        ->required(),
                    Forms\Components\DateTimePicker::make('start_date')
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->required(),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->columnSpanFull()
                        ->prefix('$'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sound.artist')
                    ->searchable()
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
                Group::make('sound.artist')
                    ->groupQueryUsing(fn(Builder $query) => $query->groupBy('sound.artist'))
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
            'index' => Pages\ListSoundReservations::route('/'),
//            'create' => Pages\CreateSoundReservation::route('/create'),
            'view' => Pages\ViewSoundReservation::route('/{record}'),
//            'edit' => Pages\EditSoundReservation::route('/{record}/edit'),
        ];
    }
}
