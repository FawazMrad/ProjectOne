<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Filament\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'tni-building';

    protected static ?string $navigationGroup = 'Venue Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('location')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('max_capacity_no_chairs')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('max_capacity_chairs')
                        ->label('Max capacity with chairs')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('vip_chairs')
                        ->required()
                        ->numeric(),
                    Forms\Components\Toggle::make('is_vip')
                        ->required(),
                    Forms\Components\TextInput::make('website')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('rating')
                        ->disabled()
                        ->numeric(),
                    Forms\Components\TextInput::make('image')
                        ->required()
                        ->url()
                        ->placeholder('https://example.com/path/to/image.jpg'),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_capacity_no_chairs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_capacity_chairs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vip_chairs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_vip')
                    ->boolean(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rating')
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
            ->filters([
                Filter::make('min_rating')
                    ->form([
                        Forms\Components\TextInput::make('rating')
                            ->label('Minimum Rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['rating'])) {
                            $rating = (float)$data['rating'];
                            return $query->where('rating', '>=', $rating);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['rating'])) {
                            return null;
                        }

                        return 'Minimum Rating: ' . (float)$data['rating'];
                    }),
                Filter::make('is_vip')
                    ->label('VIP')
                    ->query(function (Builder $query) {
                        return $query->where('is_vip', true);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\VenueReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'view' => Pages\ViewVenue::route('/{record}'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }
}
