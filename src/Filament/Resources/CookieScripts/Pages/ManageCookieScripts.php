<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieScripts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieScripts\CookieScriptResource;

class ManageCookieScripts extends ManageRecords
{
    protected static string $resource = CookieScriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
