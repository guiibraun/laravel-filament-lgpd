<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;

class CookieBannerVersionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('headline')
                    ->label('Título do aviso'),
                TextEntry::make('body')
                    ->label('Texto do aviso'),
                TextEntry::make('colors.background')
                    ->label('Cor de fundo')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['background'])
                    ->copyable(),
                TextEntry::make('colors.foreground')
                    ->label('Cor do texto')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['foreground'])
                    ->copyable(),
                TextEntry::make('colors.primary')
                    ->label('Cor de destaque')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['primary'])
                    ->copyable(),
                TextEntry::make('colors.primary_foreground')
                    ->label('Cor do texto de destaque')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['primary_foreground'])
                    ->copyable(),
                TextEntry::make('colors.border')
                    ->label('Cor da borda')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['border'])
                    ->copyable(),
                TextEntry::make('colors.overlay')
                    ->label('Cor da sobreposição')
                    ->state(fn (CookieBannerVersion $record): string => $record->publishedColors()['overlay'])
                    ->copyable(),
                TextEntry::make('published_at')
                    ->label('Publicada em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Rascunho'),
                TextEntry::make('snapshot_hash')
                    ->label('Hash do catálogo')
                    ->placeholder('—')
                    ->copyable(),
            ]);
    }
}
