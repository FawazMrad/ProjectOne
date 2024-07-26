<?php

namespace App\Filament\Resources\VenueReservationResource\Pages;

use App\Filament\Resources\VenueReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVenueReservations extends ListRecords
{
    protected static string $resource = VenueReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
