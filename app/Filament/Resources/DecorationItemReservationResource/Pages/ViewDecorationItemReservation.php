<?php

namespace App\Filament\Resources\DecorationItemReservationResource\Pages;

use App\Filament\Resources\DecorationItemReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDecorationItemReservation extends ViewRecord
{
    protected static string $resource = DecorationItemReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\EditAction::make(),
        ];
    }
}
