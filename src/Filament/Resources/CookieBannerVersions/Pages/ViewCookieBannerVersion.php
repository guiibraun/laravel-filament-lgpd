<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\CookieBannerVersionResource;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;

class ViewCookieBannerVersion extends ViewRecord
{
    protected static string $resource = CookieBannerVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('publish')
                ->label('Publicar')
                ->requiresConfirmation()
                ->visible(function (): bool {
                    $record = $this->getRecord();

                    return $record instanceof CookieBannerVersion && ! $record->isPublished();
                })
                ->action(function (): void {
                    $record = $this->getRecord();

                    if (! $record instanceof CookieBannerVersion) {
                        return;
                    }

                    $record->publish();

                    Notification::make()
                        ->title('Versão publicada')
                        ->success()
                        ->send();
                }),
        ];
    }
}
