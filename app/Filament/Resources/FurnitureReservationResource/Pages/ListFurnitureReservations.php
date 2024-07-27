<?php

namespace App\Filament\Resources\FurnitureReservationResource\Pages;

use App\Filament\Resources\FurnitureReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFurnitureReservations extends ListRecords
{
    protected static string $resource = FurnitureReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
