<?php

namespace App\Filament\Resources\DecorationCategoryResource\Pages;

use App\Filament\Resources\DecorationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDecorationCategory extends EditRecord
{
    protected static string $resource = DecorationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
