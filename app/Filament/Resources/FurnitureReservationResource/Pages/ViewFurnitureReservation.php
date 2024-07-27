<?php

namespace App\Filament\Resources\FurnitureReservationResource\Pages;

use App\Filament\Resources\FurnitureReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFurnitureReservation extends ViewRecord
{
    protected static string $resource = FurnitureReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
