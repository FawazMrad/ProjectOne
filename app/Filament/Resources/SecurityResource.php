<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityResource\Pages;
use App\Filament\Resources\SecurityResource\RelationManagers;
use App\Filament\Resources\SecurityResource\RelationManagers\SecurityReservationsRelationManager;
use App\Models\Security;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SecurityResource extends Resource
{
    protected static ?string $model = Security::class;

    protected static ?string $navigationIcon = 'fluentui-guardian-24-o';

    protected static ?string $navigationGroup = 'Security Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('clothes_color')
                        ->required()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clothes_color')
                    ->searchable(),
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
            ->filters([
                //
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
            SecurityReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecurities::route('/'),
            'create' => Pages\CreateSecurity::route('/create'),
            'view' => Pages\ViewSecurity::route('/{record}'),
            'edit' => Pages\EditSecurity::route('/{record}/edit'),
        ];
    }
}
