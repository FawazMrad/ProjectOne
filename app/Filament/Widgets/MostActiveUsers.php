<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MostActiveUsers extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static string $color = 'success';


    public function table(Table $table): Table
    {
        $query = User::query()
            ->whereDoesntHave('roles', function (Builder $roleQuery) {
                $roleQuery->where('name', 'admin');
            })
        ->where('points','>=',$this->getFifthUserPoints());
        return $table
            ->query($query)
            ->defaultSort('points', 'desc')
            ->columns([
                ImageColumn::make('profile_pic')
                    ->circular()
                    ->size(50)
                    ->label('Profile Photo'),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('phone-number')
                    ->searchable(),
                TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('qr_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->actions([
                Action::make('Award Points')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('points')
                            ->required()
                            ->label('Points to Award')
                            ->numeric()
                            ->rules(['min:0.01'])
                    ])
                    ->action(function (User $record, array $data) {
                        $record->points += $data['points'];
                        $record->save();
                    })
                    ->button()
                    ->color('primary')
                    ->icon('heroicon-o-gift')

            ])
            ->searchable(false);

    }
    public function getFifthUserPoints(): ?int
    {
        $fifthUser = User::query()
            ->whereDoesntHave('roles', function (Builder $roleQuery) {
                $roleQuery->where('name', 'admin');
            })
            ->orderBy('points', 'desc')
            ->skip(4)
            ->take(1)
            ->first();

        return $fifthUser ? $fifthUser->points : 0;
    }
}
