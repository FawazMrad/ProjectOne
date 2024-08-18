<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DecorationCategoryResource\Pages;
use App\Filament\Resources\DecorationCategoryResource\RelationManagers;
use App\Helpers\TranslationHelper;
use App\Models\DecorationCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DecorationCategoryResource extends Resource
{
    protected static ?string $model = DecorationCategory::class;

    protected static ?string $navigationIcon = 'heroicon-m-queue-list';

    protected static ?string $navigationGroup = 'Decor Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('icon')
                        ->required()
                        ->url()
                        ->placeholder('https://example.com/path/to/image.jpg'),
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
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->circular()
                    ->size(100),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description_en')
                    ->label('Description'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDecorationCategories::route('/'),
            'create' => Pages\CreateDecorationCategory::route('/create'),
            'view' => Pages\ViewDecorationCategory::route('/{record}'),
            'edit' => Pages\EditDecorationCategory::route('/{record}/edit'),
        ];
    }
}
