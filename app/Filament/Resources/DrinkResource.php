<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DrinkResource\Pages;
use App\Filament\Resources\DrinkResource\RelationManagers;
use App\Helpers\TranslationHelper;
use App\Models\Drink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DrinkResource extends Resource
{
    protected static ?string $model = Drink::class;

    protected static ?string $navigationIcon = 'fluentui-drink-wine-16-o';

    protected static ?string $navigationGroup = 'Catering Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('type')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextArea::make('description')
                        ->visible(fn($context) => $context === 'create' || $context === 'edit')
                        ->label('Description')
                        ->columnSpanFull()
                        ->maxLength(255)
                        ->afterStateUpdated(function (callable $get, callable $set) {
                            $description = $get('description');
                            if ($description) {
                                try {
                                    $translatedData = TranslationHelper::descriptionAndTranslatedDescription(['description' => $description]);
                                    $set('description_en', $translatedData['description_en']);
                                    $set('description_ar', $translatedData['description_ar']);
                                } catch (\Exception $e) {
                                    $set('description_en', '');
                                    $set('description_ar', '');
                                }
                            }
                        }),
                    Forms\Components\Hidden::make('description_en')
                        ->label('English Description'),
                    Forms\Components\Hidden::make('description_ar')
                        ->label('Arabic Description'),
                    Forms\Components\TextInput::make('cost')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                    Forms\Components\TextInput::make('age_required')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('image')
                        ->url()
                        ->required()
                        ->placeholder('https://example.com/path/to/image.jpg')
                        ->columnSpanFull(),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description_en')
                    ->label('Description'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age_required')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50),
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
            RelationManagers\DrinkReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrinks::route('/'),
            'create' => Pages\CreateDrink::route('/create'),
            'view' => Pages\ViewDrink::route('/{record}'),
            'edit' => Pages\EditDrink::route('/{record}/edit'),
        ];
    }
}
