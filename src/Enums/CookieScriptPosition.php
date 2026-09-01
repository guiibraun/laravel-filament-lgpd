<?php

namespace Guiibraun\FilamentLgpd\Enums;

use Filament\Support\Contracts\HasLabel;

enum CookieScriptPosition: string implements HasLabel
{
    case Head = 'head';
    case BodyStart = 'body_start';
    case BodyEnd = 'body_end';

    public function label(): string
    {
        return match ($this) {
            self::Head => 'Head',
            self::BodyStart => 'Body — início',
            self::BodyEnd => 'Body — final',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
