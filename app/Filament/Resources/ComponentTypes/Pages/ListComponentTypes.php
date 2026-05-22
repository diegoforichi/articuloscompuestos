<?php

namespace App\Filament\Resources\ComponentTypes\Pages;

use App\Filament\Resources\ComponentTypes\ComponentTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComponentTypes extends ListRecords
{
    protected static string $resource = ComponentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
