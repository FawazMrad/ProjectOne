<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DecorationItemResource\Pages;
use App\Filament\Resources\DecorationItemResource\RelationManagers;
use App\Filament\Resources\DecorationItemResource\RelationManagers\DecorationItemReservationsRelationManager;
use App\Helpers\TranslationHelper;
use App\Models\DecorationItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DecorationItemResource extends Resource
{
    protected static ?string $model = DecorationItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Decor Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Select::make('decoration_category_id')
                        ->relationship(name: 'decorationCategory', titleAttribute: 'name')
                        ->label('Category')
                        ->searchable()
                        ->native(false)
                        ->preload()
                        ->required(),
                    Forms\Components\TextArea::make('description')
                        ->visible(fn($context) => $context === 'create' || $context === 'edit')
                        ->label('Description')
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
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
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
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('decorationCategory.name')
                ->searchable(),
                Tables\Columns\TextColumn::make('description_en')
                    ->label('Description'),
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
                SelectFilter::make('Category')
                    ->relationship(name: 'decorationCategory', titleAttribute: 'name'),
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
            DecorationItemReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDecorationItems::route('/'),
            'create' => Pages\CreateDecorationItem::route('/create'),
            'view' => Pages\ViewDecorationItem::route('/{record}'),
            'edit' => Pages\EditDecorationItem::route('/{record}/edit'),
        ];
    }
}
