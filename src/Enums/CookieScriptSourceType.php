<?php

namespace Guiibraun\FilamentLgpd\Enums;

use Filament\Support\Contracts\HasLabel;

enum CookieScriptSourceType: string implements HasLabel
{
    case External = 'external';
    case Inline = 'inline';

    public function label(): string
    {
        return match ($this) {
            self::External => 'URL externa',
            self::Inline => 'Código inline',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
