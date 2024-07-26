<?php

namespace App\Filament\Resources\DecorationItemResource\Pages;

use App\Filament\Resources\DecorationItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDecorationItem extends ViewRecord
{
    protected static string $resource = DecorationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
