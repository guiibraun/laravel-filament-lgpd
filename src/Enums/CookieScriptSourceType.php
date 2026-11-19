<?php

namespace Guiibraun\FilamentLgpd\Enums;

enum CookieScriptSourceType: string
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

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
