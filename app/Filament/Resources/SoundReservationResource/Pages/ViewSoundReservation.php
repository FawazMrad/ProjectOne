<?php

namespace App\Filament\Resources\SoundReservationResource\Pages;

use App\Filament\Resources\SoundReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSoundReservation extends ViewRecord
{
    protected static string $resource = SoundReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
