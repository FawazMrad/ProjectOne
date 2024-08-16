<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityReservationResource\Pages;
use App\Filament\Resources\SecurityReservationResource\RelationManagers;
use App\Models\SecurityReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SecurityReservationResource extends Resource
{
    protected static ?string $model = SecurityReservation::class;

    protected static ?string $navigationIcon = 'bi-calendar2-check';

    protected static ?string $navigationGroup = 'Security Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('security_id')
                        ->relationship(name: 'security', titleAttribute: 'clothes_color')
                        ->label('Security Clothes Color')
                        ->required(),
                    Forms\Components\Select::make('event_id')
                        ->relationship(name: 'event', titleAttribute: 'title')
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
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('security.clothes_color')
                    ->label('Security Clothes')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.title')
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
                Group::make('security.clothes_color')
                    ->groupQueryUsing(fn(Builder $query) => $query->groupBy('security.clothes_color'))
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
            'index' => Pages\ListSecurityReservations::route('/'),
//            'create' => Pages\CreateSecurityReservation::route('/create'),
            'view' => Pages\ViewSecurityReservation::route('/{record}'),
//            'edit' => Pages\EditSecurityReservation::route('/{record}/edit'),
        ];
    }
}
