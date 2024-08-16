<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Users' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin')
                ))
                ->badge(User::query()->where(fn(Builder $query): Builder => $query->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin')
                ))->count()),
            'Admins' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereHas('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin')
                ))
                ->badge(User::query()->where(fn(Builder $query): Builder => $query->whereHas('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin')
                ))->count()),

        ];
    }
}
