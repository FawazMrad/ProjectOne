<?php

namespace App\Filament\Resources\FoodReservationResource\Pages;

use App\Filament\Resources\FoodReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFoodReservations extends ListRecords
{
    protected static string $resource = FoodReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
