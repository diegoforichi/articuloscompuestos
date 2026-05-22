<?php

namespace App\Filament\Resources\ComponentTypes\Pages;

use App\Filament\Resources\ComponentTypes\ComponentTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditComponentType extends EditRecord
{
    protected static string $resource = ComponentTypeResource::class;
}
