<?php

namespace App\Filament\Resources\DecorationItemReservationResource\Pages;

use App\Filament\Resources\DecorationItemReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDecorationItemReservation extends EditRecord
{
    protected static string $resource = DecorationItemReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
