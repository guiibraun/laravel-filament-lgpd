<?php

namespace Guiibraun\FilamentLgpd\Enums;

use Filament\Support\Contracts\HasLabel;

enum CookieConsentAction: string implements HasLabel
{
    case AcceptAll = 'accept_all';
    case RejectNonEssential = 'reject_non_essential';
    case Customize = 'customize';

    public function label(): string
    {
        return match ($this) {
            self::AcceptAll => 'Aceitar todos',
            self::RejectNonEssential => 'Rejeitar não necessários',
            self::Customize => 'Personalizar',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
