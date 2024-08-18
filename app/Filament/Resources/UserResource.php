<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BaconQrCode\Renderer\RendererStyle\Fill;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    public static function getNavigationBadge(): ?string
    {
        return User::query()->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin'))->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Name')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),
                Forms\Components\Section::make('Email Verification')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(100),
                    ]),
                Forms\Components\Section::make('Contact Info')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),
                Forms\Components\Section::make('Other Details')
                    ->schema([
                        Forms\Components\TextInput::make('profile_pic')
                            ->label('Profile photo'),
                        Forms\Components\DatePicker::make('birth_date'),
                        Forms\Components\TextInput::make('points')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('rating')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('qr_code')
                            ->maxLength(255)
                            ->columnSpanFull()
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns
            ([
                Tables\Columns\ImageColumn::make('profile_pic')
                    ->getStateUsing(function ($record) {
                        if ($record->profile_pic) {
                            return 'data:image/jpeg;base64,' . $record->profile_pic;  // Adjust MIME type if necessary
                        }
                        return null;
                    })
                    ->circular()
                    ->size(100)
                    ->label('Profile Photo'),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->label('Name')
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->getStateUsing(function ($record) {
                        if ($record->qr_code) {
                            return 'data:image/svg+xml;base64,' . $record->qr_code;
                        }
                        return null;
                    })
                    ->size(100)
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
                Filter::make('age')
                    ->form([
                        Forms\Components\TextInput::make('age')
                            ->label('Age')
                            ->numeric()
                            ->minValue(0)
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['age'])) {
                            $age = (int)$data['age'];
                            $date = now()->subYears($age)->format('Y-m-d');
                            return $query->whereDate('birth_date', '<=', $date)
                                ->whereDate('birth_date', '>', now()->subYears($age + 1)->format('Y-m-d'));
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['age'])) {
                            return null;
                        }

                        return 'Age: ' . (int)$data['age'];
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
                    })
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            //'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            //'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
