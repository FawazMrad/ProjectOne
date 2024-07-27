<?php

namespace App\Filament\Resources\FoodReservationResource\Pages;

use App\Filament\Resources\FoodReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoodReservation extends EditRecord
{
    protected static string $resource = FoodReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
