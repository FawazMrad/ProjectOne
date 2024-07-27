<?php

namespace App\Filament\Resources\DrinkReservationResource\Pages;

use App\Filament\Resources\DrinkReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDrinkReservation extends ViewRecord
{
    protected static string $resource = DrinkReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
