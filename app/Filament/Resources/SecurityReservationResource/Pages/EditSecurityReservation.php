<?php

namespace App\Filament\Resources\SecurityReservationResource\Pages;

use App\Filament\Resources\SecurityReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSecurityReservation extends EditRecord
{
    protected static string $resource = SecurityReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
