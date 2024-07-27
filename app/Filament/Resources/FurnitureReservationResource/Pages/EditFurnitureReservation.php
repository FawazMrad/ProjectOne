<?php

namespace App\Filament\Resources\FurnitureReservationResource\Pages;

use App\Filament\Resources\FurnitureReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFurnitureReservation extends EditRecord
{
    protected static string $resource = FurnitureReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
