<?php

use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;
use Guiibraun\FilamentLgpd\Models\CookieCategory;
use Guiibraun\FilamentLgpd\Models\CookieConsent;
use Guiibraun\FilamentLgpd\Models\CookieDefinition;
use Guiibraun\FilamentLgpd\Models\PrivacyPolicy;

return [
    'routes' => [
        'enabled' => true,
        'prefix' => '',
        'middleware' => ['web'],
        'privacy_path' => '/privacidade',
        'cookies_path' => '/cookies',
        'consent_path' => '/cookies/consent',
        'privacy_name' => 'privacy',
        'cookies_name' => 'cookies',
        'consent_name' => 'cookies.consent',
        'throttle' => '30,1',
    ],

    'models' => [
        'privacy_policy' => PrivacyPolicy::class,
        'cookie_category' => CookieCategory::class,
        'cookie_definition' => CookieDefinition::class,
        'cookie_banner_version' => CookieBannerVersion::class,
        'cookie_consent' => CookieConsent::class,
    ],

    'cache' => [
        'privacy_policy' => 'privacy-policy.public',
        'cookie_banner' => 'cookie-banner.published',
    ],

    'cookie' => [
        'name' => 'lgpd_consent',
        'minutes' => 60 * 24 * 365,
        'domain' => null,
        'secure' => null,
        'http_only' => true,
        'raw' => false,
        'same_site' => 'lax',
    ],

    'inertia' => [
        'enabled' => true,
        'privacy_component' => 'Privacy',
        'cookies_component' => 'Cookies',
        'cookie_banner_prop' => 'cookieBanner',
        'cookie_consent_prop' => 'cookieConsent',
        'home_label' => 'Início',
        'home_url' => '/',
        'privacy_label' => 'Privacidade',
        'cookies_label' => 'Cookies',
    ],

    'filament' => [
        'register_resources' => true,
        'register_pages' => true,
    ],

    'retention_years' => 5,
    'schedule_pruning' => true,

    'catalog' => [
        'categories' => [
            'necessary' => [
                'name' => 'Necessários',
                'description' => 'Essenciais para o site funcionar, a segurança e para lembrar a sua escolha de cookies. Não podem ser desligados.',
                'is_required' => true,
                'sort_order' => 1,
            ],
            'analytics' => [
                'name' => 'Analíticos',
                'description' => 'Ajudam a entender como o site é usado. Só são ligados se você permitir.',
                'is_required' => false,
                'sort_order' => 2,
            ],
            'marketing' => [
                'name' => 'Marketing',
                'description' => 'Tags de publicidade e redes. Só são ligados se você permitir.',
                'is_required' => false,
                'sort_order' => 3,
            ],
        ],
        'definitions' => [
            [
                'category' => 'necessary',
                'name' => '{session}',
                'provider' => 'Aplicação',
                'duration' => '{session_duration}',
                'purpose' => 'Mantém a sessão de navegação no site.',
                'is_first_party' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'necessary',
                'name' => 'XSRF-TOKEN',
                'provider' => 'Aplicação',
                'duration' => 'Sessão',
                'purpose' => 'Protege formulários contra requisições forjadas.',
                'is_first_party' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'necessary',
                'name' => '{consent_cookie}',
                'provider' => 'Aplicação',
                'duration' => '12 meses',
                'purpose' => 'Guarda o identificador anônimo da sua preferência de cookies.',
                'is_first_party' => true,
                'sort_order' => 3,
            ],
        ],
        'banner' => [
            'headline' => 'Usamos cookies',
            'body' => 'Usamos cookies necessários para o site funcionar. Cookies analíticos e de marketing só são ligados se você permitir. Você pode aceitar, recusar os não necessários ou gerenciar as categorias.',
            'colors' => CookieBannerVersion::DEFAULT_COLORS,
        ],
    ],
];
