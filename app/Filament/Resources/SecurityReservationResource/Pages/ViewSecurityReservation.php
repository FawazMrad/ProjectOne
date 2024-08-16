<?php

namespace App\Filament\Resources\SecurityReservationResource\Pages;

use App\Filament\Resources\SecurityReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSecurityReservation extends ViewRecord
{
    protected static string $resource = SecurityReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
