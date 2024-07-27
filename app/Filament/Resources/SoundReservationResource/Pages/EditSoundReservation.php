<?php

namespace App\Filament\Resources\SoundReservationResource\Pages;

use App\Filament\Resources\SoundReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoundReservation extends EditRecord
{
    protected static string $resource = SoundReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
