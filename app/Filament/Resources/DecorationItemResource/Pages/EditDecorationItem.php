<?php

namespace App\Filament\Resources\DecorationItemResource\Pages;

use App\Filament\Resources\DecorationItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDecorationItem extends EditRecord
{
    protected static string $resource = DecorationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
