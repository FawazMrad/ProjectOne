<?php

namespace App\Filament\Resources\SoundResource\Pages;

use App\Filament\Resources\SoundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSounds extends ListRecords
{
    protected static string $resource = SoundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
