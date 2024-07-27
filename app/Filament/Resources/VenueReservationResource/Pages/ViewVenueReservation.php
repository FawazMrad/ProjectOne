<?php

namespace App\Filament\Resources\VenueReservationResource\Pages;

use App\Filament\Resources\VenueReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVenueReservation extends ViewRecord
{
    protected static string $resource = VenueReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
