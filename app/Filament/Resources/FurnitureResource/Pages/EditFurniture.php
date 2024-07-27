<?php

namespace App\Filament\Resources\FurnitureResource\Pages;

use App\Filament\Resources\FurnitureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFurniture extends EditRecord
{
    protected static string $resource = FurnitureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
