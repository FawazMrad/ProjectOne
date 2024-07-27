<?php

namespace App\Filament\Resources\FurnitureResource\Pages;

use App\Filament\Resources\FurnitureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFurniture extends ListRecords
{
    protected static string $resource = FurnitureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
