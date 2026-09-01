<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

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
