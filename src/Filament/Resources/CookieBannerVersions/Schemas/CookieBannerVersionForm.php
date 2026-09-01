<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CookieBannerVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('headline')
                    ->label('Título do aviso')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label('Texto do aviso')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
