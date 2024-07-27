<?php

namespace App\Filament\Resources\DecorationCategoryResource\Pages;

use App\Filament\Resources\DecorationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDecorationCategory extends ViewRecord
{
    protected static string $resource = DecorationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
