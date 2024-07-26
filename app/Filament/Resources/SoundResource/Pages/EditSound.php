<?php

namespace App\Filament\Resources\SoundResource\Pages;

use App\Filament\Resources\SoundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSound extends EditRecord
{
    protected static string $resource = SoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
