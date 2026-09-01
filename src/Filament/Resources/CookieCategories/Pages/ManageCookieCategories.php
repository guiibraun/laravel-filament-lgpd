<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieCategories\Pages;

use Filament\Resources\Pages\ManageRecords;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieCategories\CookieCategoryResource;

class ManageCookieCategories extends ManageRecords
{
    protected static string $resource = CookieCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
