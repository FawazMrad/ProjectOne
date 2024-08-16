<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DrinkResource\Pages\CreateDrink;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Helpers\TranslationHelper;
use App\Models\Event;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Filters;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'lucide-party-popper';

    protected static ?string $navigationGroup = 'Events Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Organizer')
                            ->options(function () {
                                return User::all()->pluck('full_name', 'id');
                            })
                            //->relationship('user', 'first_name')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('category_id')
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->required(),
                        Select::make('attendance_type')
                            ->options(['INVITATION' => 'INVITATION', 'TICKET' => 'TICKET'])
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Description')
                    ->schema([
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
                    ])->visible(fn($context) => $context === 'create' || $context === 'edit')
                ,
                Forms\Components\Section::make('Date and Time')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date'),
                        Forms\Components\DateTimePicker::make('end_date'),
                    ])->columns(2),
                Forms\Components\Section::make('Cost')
                    ->schema([
                        Forms\Components\TextInput::make('total_cost')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('ticket_price')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('vip_ticket_price')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                    ])->columns(3),
                Forms\Components\Section::make('Other Details')
                    ->schema([
                        Forms\Components\Toggle::make('is_paid')
                            ->required(),
                        Forms\Components\Toggle::make('is_private')
                            ->required(),
                        Forms\Components\TextInput::make('min_age')
                            ->numeric(),
                        Forms\Components\TextInput::make('image')
                            ->url()
                            ->placeholder('https://example.com/path/to/image.jpg'),
                        Forms\Components\TextInput::make('qr_code')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->default(0.00),
                    ])->columns(2),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(100),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->numeric()
                    ->sortable(['first_name', 'last_name'])
                    ->label('Organizer'),
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\columns\TextColumn::make('description_en')
                    ->wrap()
                    ->words(8)
                    ->label('Description'),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_type')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->getStateUsing(function ($record) {
                        if ($record->qr_code) {
                            return 'data:image/svg+xml;base64,' . $record->qr_code;
                        }
                        return null;
                    })
                    ->size(100),
                Tables\Columns\TextColumn::make('min_age')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_private')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_cost')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ticket_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vip_ticket_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('min_age')
                    ->form([
                        Forms\Components\TextInput::make('min_age')
                            ->label('Minimum Age')
                            ->numeric()
                            ->minValue(0)
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['min_age'])) {
                            $min_age = (int)$data['min_age'];
                            return $query->where('min_age', '>=', $min_age);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['min_age'])) {
                            return null;
                        }

                        return 'Min Age: ' . (float)$data['min_age'];
                    }),
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
                SelectFilter::make('attendance_type')
                    ->label('Attendance Type')
                    ->options([
                        'INVITATION' => 'Invitation',
                        'TICKET' => 'Ticket',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            return $query->where('attendance_type', $data['value']);
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['value'])) {
                            return null;
                        }

                        return 'Attendance Type: ' . ucfirst(strtolower($data['value']));
                    }),
                SelectFilter::make('Category')
                    ->relationship(name: 'category', titleAttribute: 'name'),

                Filter::make('is_paid')
                    ->label('Paid')
                    ->query(function (Builder $query) {
                        return $query->where('is_paid', true);
                    }),
                Filter::make('is_private')
                    ->label('Private')
                    ->query(function (Builder $query) {
                        return $query->where('is_private', true);
                    }),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                ->columns(2)
                ->columnSpanFull()
                    ->indicateUsing(function (array $data): ?string {
                        $from = $data['created_from'] ?? null;
                        $until = $data['created_until'] ?? null;

                        if (!$from && !$until) {
                            return null; // No indication if neither date is provided
                        }

                        $fromText = $from ? 'From: ' . \Carbon\Carbon::parse($from)->format('M d, Y') : '';
                        $untilText = $until ? 'Until: ' . \Carbon\Carbon::parse($until)->format('M d, Y') : '';

                        return trim($fromText . ' ' . $untilText);
                    }),
            ], Tables\Enums\FiltersLayout::Modal)->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
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
            RelationManagers\VenueReservationRelationManager::class,
            RelationManagers\FurnitureReservationsRelationManager::class,
            RelationManagers\DecorationItemReservationsRelationManager::class,
            RelationManagers\SoundReservationsRelationManager::class,
            RelationManagers\FoodReservationsRelationManager::class,
            RelationManagers\DrinkReservationsRelationManager::class,
            RelationManagers\SecurityReservationsRelationManager::class,
            RelationManagers\AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            //'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            //'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
