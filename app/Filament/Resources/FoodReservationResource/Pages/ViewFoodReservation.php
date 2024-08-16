<?php

namespace App\Filament\Resources\FoodReservationResource\Pages;

use App\Filament\Resources\FoodReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFoodReservation extends ViewRecord
{
    protected static string $resource = FoodReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
