<?php

namespace App\Filament\Resources\SecurityResource\Pages;

use App\Filament\Resources\SecurityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSecurity extends EditRecord
{
    protected static string $resource = SecurityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
