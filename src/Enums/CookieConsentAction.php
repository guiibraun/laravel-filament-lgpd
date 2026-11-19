<?php

namespace Guiibraun\FilamentLgpd\Enums;

enum CookieConsentAction: string
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
}
