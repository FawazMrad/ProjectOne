<?php

namespace App\Filament\Resources\DrinkReservationResource\Pages;

use App\Filament\Resources\DrinkReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrinkReservations extends ListRecords
{
    protected static string $resource = DrinkReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
