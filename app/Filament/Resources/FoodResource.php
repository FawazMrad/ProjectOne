<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Helpers\TranslationHelper;
use App\Models\Food;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'fluentui-food-16-o';

    protected static ?string $navigationGroup = 'Catering Management';

    protected static ?int $navigationSort = 1;

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
                    Forms\Components\TextInput::make('image')
                        ->url()
                        ->required()
                        ->placeholder('https://example.com/path/to/image.jpg'),
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
            RelationManagers\FoodReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'view' => Pages\ViewFood::route('/{record}'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }
}
