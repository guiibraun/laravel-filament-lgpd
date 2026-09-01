<?php

namespace Guiibraun\FilamentLgpd\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Guiibraun\FilamentLgpd\Enums\CookieConsentAction;
use Guiibraun\FilamentLgpd\Http\Requests\StoreCookieConsentRequest;
use Guiibraun\FilamentLgpd\Http\Resources\CookieBannerResource;
use Guiibraun\FilamentLgpd\Models\CookieConsent;

class CookieController
{
    public function policy(): Response
    {
        $banner = CookieBannerResource::current();

        abort_unless(is_array($banner), 404);

        return Inertia::render((string) config('filament-lgpd.inertia.cookies_component', 'Cookies'), [
            'breadcrumb' => [
                [
                    'label' => (string) config('filament-lgpd.inertia.home_label', 'Início'),
                    'url' => config('filament-lgpd.inertia.home_url', '/'),
                ],
                [
                    'label' => (string) config('filament-lgpd.inertia.cookies_label', 'Cookies'),
                    'url' => null,
                ],
            ],
            'banner' => $banner,
        ]);
    }

    public function store(StoreCookieConsentRequest $request): RedirectResponse
    {
        $cookieName = config('filament-lgpd.cookie.name', CookieConsent::VISITOR_COOKIE);

        if (! is_string($cookieName) || $cookieName === '') {
            $cookieName = CookieConsent::VISITOR_COOKIE;
        }

        $visitorCookie = $request->cookie($cookieName);
        $visitorId = is_string($visitorCookie) && Str::isUuid($visitorCookie)
            ? $visitorCookie
            : (string) Str::uuid();

        /** @var class-string<CookieConsent> $consentClass */
        $consentClass = (string) config('filament-lgpd.models.cookie_consent', CookieConsent::class);

        $consentClass::create([
            'visitor_id' => $visitorId,
            'cookie_banner_version_id' => $request->integer('banner_version_id'),
            'action' => $request->enum('action', CookieConsentAction::class),
            'choices' => $request->resolvedChoices(),
            'source' => $request->validated('source'),
            'locale' => app()->getLocale(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512),
        ]);

        $secure = config('filament-lgpd.cookie.secure');

        if ($secure === null) {
            $secure = config('session.secure', false);
        }

        Cookie::queue(cookie(
            $cookieName,
            $visitorId,
            (int) config('filament-lgpd.cookie.minutes', CookieConsent::VISITOR_COOKIE_MINUTES),
            '/',
            config('filament-lgpd.cookie.domain', config('session.domain')),
            (bool) $secure,
            (bool) config('filament-lgpd.cookie.http_only', true),
            (bool) config('filament-lgpd.cookie.raw', false),
            config('filament-lgpd.cookie.same_site', 'lax'),
        ));

        return back();
    }
}
