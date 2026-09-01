<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Pages;

use Filament\Resources\Pages\ViewRecord;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\CookieConsentResource;

class ViewCookieConsent extends ViewRecord
{
    protected static string $resource = CookieConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
