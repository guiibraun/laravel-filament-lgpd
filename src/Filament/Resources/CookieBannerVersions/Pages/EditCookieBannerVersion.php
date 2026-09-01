<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\CookieBannerVersionResource;

class EditCookieBannerVersion extends EditRecord
{
    protected static string $resource = CookieBannerVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
