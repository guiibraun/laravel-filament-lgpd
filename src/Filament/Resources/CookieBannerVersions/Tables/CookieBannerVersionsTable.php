<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;

class CookieBannerVersionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('headline')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label('Publicada em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Rascunho')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (CookieBannerVersion $record): bool => ! $record->isPublished()),
                Action::make('publish')
                    ->label('Publicar')
                    ->requiresConfirmation()
                    ->visible(fn (CookieBannerVersion $record): bool => ! $record->isPublished())
                    ->action(function (CookieBannerVersion $record): void {
                        $record->publish();

                        Notification::make()
                            ->title('Versão publicada')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->visible(fn (CookieBannerVersion $record): bool => ! $record->isPublished()),
            ]);
    }
}
