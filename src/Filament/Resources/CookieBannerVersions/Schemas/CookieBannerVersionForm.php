<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;

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
                Section::make('Cores do popup')
                    ->description('Essas cores são publicadas junto com o texto e aplicadas ao banner e ao painel de preferências no frontend.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('colors.background')
                                    ->label('Fundo')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['background'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                                ColorPicker::make('colors.foreground')
                                    ->label('Texto')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['foreground'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                                ColorPicker::make('colors.primary')
                                    ->label('Destaque')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['primary'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                                ColorPicker::make('colors.primary_foreground')
                                    ->label('Texto do destaque')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['primary_foreground'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                                ColorPicker::make('colors.border')
                                    ->label('Borda')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['border'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                                ColorPicker::make('colors.overlay')
                                    ->label('Sobreposição')
                                    ->helperText('Aceita transparência no formato HEX de 8 dígitos, como #00000080.')
                                    ->default(fn (): string => CookieBannerVersion::defaultColors()['overlay'])
                                    ->hexColor()
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
