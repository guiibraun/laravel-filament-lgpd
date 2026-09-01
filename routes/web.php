<?php

use Illuminate\Support\Facades\Route;
use Guiibraun\FilamentLgpd\Http\Controllers\CookieController;
use Guiibraun\FilamentLgpd\Http\Controllers\PrivacyController;

Route::middleware(config('filament-lgpd.routes.middleware', ['web']))
    ->prefix(trim((string) config('filament-lgpd.routes.prefix', ''), '/'))
    ->group(function (): void {
        Route::get(
            (string) config('filament-lgpd.routes.privacy_path', '/privacidade'),
            PrivacyController::class,
        )->name((string) config('filament-lgpd.routes.privacy_name', 'privacy'));

        Route::get(
            (string) config('filament-lgpd.routes.cookies_path', '/cookies'),
            [CookieController::class, 'policy'],
        )->name((string) config('filament-lgpd.routes.cookies_name', 'cookies'));

        Route::post(
            (string) config('filament-lgpd.routes.consent_path', '/cookies/consent'),
            [CookieController::class, 'store'],
        )
            ->middleware('throttle:'.config('filament-lgpd.routes.throttle', '30,1'))
            ->name((string) config('filament-lgpd.routes.consent_name', 'cookies.consent'));
    });
