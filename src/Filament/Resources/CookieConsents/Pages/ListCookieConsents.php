<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Pages;

use Filament\Resources\Pages\ListRecords;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\CookieConsentResource;

class ListCookieConsents extends ListRecords
{
    protected static string $resource = CookieConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
