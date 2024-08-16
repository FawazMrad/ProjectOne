<?php

namespace App\Filament\Resources\VenueReservationResource\Pages;

use App\Filament\Resources\VenueReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenueReservation extends EditRecord
{
    protected static string $resource = VenueReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            //Actions\DeleteAction::make(),
        ];
    }
}
