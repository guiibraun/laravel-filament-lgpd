<?php

namespace Guiibraun\FilamentLgpd;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentLgpdPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-lgpd';
    }

    public function register(Panel $panel): void
    {
        if (config('filament-lgpd.filament.register_resources', true)) {
            $panel->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'Guiibraun\\FilamentLgpd\\Filament\\Resources',
            );
        }

        if (config('filament-lgpd.filament.register_pages', true)) {
            $panel->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Guiibraun\\FilamentLgpd\\Filament\\Pages',
            );
        }
    }

    public function boot(Panel $panel): void {}
}
