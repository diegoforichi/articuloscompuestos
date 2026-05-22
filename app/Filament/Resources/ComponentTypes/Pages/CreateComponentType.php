<?php

namespace App\Filament\Resources\ComponentTypes\Pages;

use App\Filament\Resources\ComponentTypes\ComponentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComponentType extends CreateRecord
{
    protected static string $resource = ComponentTypeResource::class;
}
