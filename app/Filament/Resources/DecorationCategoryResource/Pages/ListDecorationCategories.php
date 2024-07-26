<?php

namespace App\Filament\Resources\DecorationCategoryResource\Pages;

use App\Filament\Resources\DecorationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDecorationCategories extends ListRecords
{
    protected static string $resource = DecorationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
