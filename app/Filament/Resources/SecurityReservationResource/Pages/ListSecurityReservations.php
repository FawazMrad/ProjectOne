<?php

namespace App\Filament\Resources\SecurityReservationResource\Pages;

use App\Filament\Resources\SecurityReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSecurityReservations extends ListRecords
{
    protected static string $resource = SecurityReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
