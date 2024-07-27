<?php

namespace App\Filament\Resources\FurnitureResource\Pages;

use App\Filament\Resources\FurnitureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFurniture extends ViewRecord
{
    protected static string $resource = FurnitureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
