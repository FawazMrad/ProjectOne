<?php

namespace App\Filament\Resources\DecorationItemResource\Pages;

use App\Filament\Resources\DecorationItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDecorationItems extends ListRecords
{
    protected static string $resource = DecorationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
