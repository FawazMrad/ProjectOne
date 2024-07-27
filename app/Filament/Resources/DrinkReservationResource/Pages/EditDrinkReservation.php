<?php

namespace App\Filament\Resources\DrinkReservationResource\Pages;

use App\Filament\Resources\DrinkReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDrinkReservation extends EditRecord
{
    protected static string $resource = DrinkReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
