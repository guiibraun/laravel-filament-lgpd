<?php

namespace Guiibraun\FilamentLgpd;

use Illuminate\Support\Facades\Schedule;
use Inertia\Inertia;
use Guiibraun\FilamentLgpd\Console\Commands\PruneCookieConsentsCommand;
use Guiibraun\FilamentLgpd\Console\Commands\SeedCookieCatalogCommand;
use Guiibraun\FilamentLgpd\Http\Resources\CookieBannerResource;
use Guiibraun\FilamentLgpd\Models\CookieConsent;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentLgpdServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-lgpd';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews('filament-lgpd')
            ->hasMigrations([
                'create_privacy_policies_table',
                'create_cookie_categories_table',
                'create_cookie_definitions_table',
                'create_cookie_banner_versions_table',
                'add_colors_to_cookie_banner_versions_table',
                'create_cookie_consents_table',
            ])
            ->hasCommands([
                PruneCookieConsentsCommand::class,
                SeedCookieCatalogCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });

        if (config('filament-lgpd.routes.enabled', true)) {
            $package->hasRoute('web');
        }
    }

    public function packageRegistered(): void
    {
        if (! config('filament-lgpd.inertia.enabled', true)) {
            return;
        }

        Inertia::share([
            (string) config('filament-lgpd.inertia.cookie_banner_prop', 'cookieBanner') => fn (): ?array => CookieBannerResource::current(),
            (string) config('filament-lgpd.inertia.cookie_consent_prop', 'cookieConsent') => function (): ?array {
                /** @var class-string<CookieConsent> $consentClass */
                $consentClass = (string) config('filament-lgpd.models.cookie_consent', CookieConsent::class);

                $visitorId = request()->cookie(
                    (string) config('filament-lgpd.cookie.name', CookieConsent::VISITOR_COOKIE),
                );
                $consent = $consentClass::latestForVisitor(
                    is_string($visitorId) ? $visitorId : null,
                );

                if (! $consent instanceof CookieConsent) {
                    return null;
                }

                return [
                    'bannerVersionId' => $consent->cookie_banner_version_id,
                    'action' => $consent->action->value,
                    'choices' => $consent->choices,
                ];
            },
        ]);
    }

    public function packageBooted(): void
    {
        if (config('filament-lgpd.schedule_pruning', true)) {
            Schedule::command(PruneCookieConsentsCommand::class)
                ->daily()
                ->withoutOverlapping();
        }
    }
}
