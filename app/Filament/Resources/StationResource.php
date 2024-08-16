<?php

namespace App\Filament\Resources;

use AnourValar\EloquentSerialize\Tests\Models\Post;
use App\Filament\Resources\StationResource\Pages;
use App\Filament\Resources\StationResource\RelationManagers;
use App\Models\Station;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StationResource extends Resource
{
    protected static ?string $model = Station::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Balance Station Management';

    protected static ?string $navigationLabel = 'Balance Station';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Select::make('governorate')
                        ->options([
                            'Damascus' => 'DAMASCUS', 'Aleppo' => 'ALEPPO', 'Idlib' => 'IDLIB', 'Hamah' => 'HAMAH', 'Lattakia' => 'LATTAKIA',
                            'Tartous' => 'TARTOUS', 'Homs' => 'HOMS', 'Swaida' => 'SWAIDA', 'Daraa' => 'DARAA', 'Quanytira' => 'QUANYTIRA',
                            'DayrAlzwr' => 'DAYRALZWR', 'Alhasakah' => 'ALHASAKAH', 'Alraqqah' => 'ALRAQQAH', 'RifDimashq' => 'RIFDIMASHQ'
                        ])
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('location')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('manager_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('manager_email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('manager_id_picture')
                        ->required(),
                    Forms\Components\TextInput::make('balance')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('governorate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_email')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('manager_id_picture')
                    ->size(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric()
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
                Action::make('Add Balance')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->label('Amount to Add')
                            ->numeric()
                            ->rules(['min:0.01'])
                    ])
                    ->action(function (Station $record, array $data) {
                        $record->balance += $data['amount'];
                        $record->save();
                    })
                    ->button()
                    ->color('primary')
                    ->icon('css-add')
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
            'index' => Pages\ListStations::route('/'),
            'create' => Pages\CreateStation::route('/create'),
            'view' => Pages\ViewStation::route('/{record}'),
            'edit' => Pages\EditStation::route('/{record}/edit'),
        ];
    }
}
