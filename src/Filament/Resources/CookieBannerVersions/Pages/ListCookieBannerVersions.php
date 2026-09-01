<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\CookieBannerVersionResource;

class ListCookieBannerVersions extends ListRecords
{
    protected static string $resource = CookieBannerVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
