<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieDefinitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieDefinitions\CookieDefinitionResource;

class ManageCookieDefinitions extends ManageRecords
{
    protected static string $resource = CookieDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
