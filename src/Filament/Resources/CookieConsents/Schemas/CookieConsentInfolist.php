<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CookieConsentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('action')
                    ->label('Escolha'),
                TextEntry::make('source')
                    ->label('Origem'),
                TextEntry::make('visitor_id')
                    ->label('Visitante')
                    ->copyable(),
                TextEntry::make('cookie_banner_version_id')
                    ->label('Versão do aviso'),
                TextEntry::make('locale')
                    ->label('Idioma'),
                TextEntry::make('user_agent')
                    ->label('Navegador')
                    ->placeholder('—'),
                KeyValueEntry::make('choices')
                    ->label('Categorias'),
            ]);
    }
}
