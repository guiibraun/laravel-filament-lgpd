<?php

namespace Guiibraun\FilamentLgpd\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;
use Guiibraun\FilamentLgpd\Models\CookieCategory;
use Guiibraun\FilamentLgpd\Models\CookieConsent;
use Guiibraun\FilamentLgpd\Models\CookieDefinition;

#[Signature('filament-lgpd:seed-catalog')]
#[Description('Seed the default LGPD cookie catalog and first banner version')]
class SeedCookieCatalogCommand extends Command
{
    public function handle(): int
    {
        /** @var class-string<CookieCategory> $categoryClass */
        $categoryClass = (string) config('filament-lgpd.models.cookie_category', CookieCategory::class);
        /** @var class-string<CookieDefinition> $definitionClass */
        $definitionClass = (string) config('filament-lgpd.models.cookie_definition', CookieDefinition::class);
        /** @var class-string<CookieBannerVersion> $bannerClass */
        $bannerClass = (string) config('filament-lgpd.models.cookie_banner_version', CookieBannerVersion::class);

        $categories = [];

        foreach (config('filament-lgpd.catalog.categories', []) as $slug => $attributes) {
            $categories[$slug] = $categoryClass::query()->updateOrCreate(
                ['slug' => $slug],
                $attributes,
            );
        }

        foreach (config('filament-lgpd.catalog.definitions', []) as $definition) {
            $category = $categories[$definition['category']] ?? null;

            if (! $category instanceof CookieCategory) {
                continue;
            }

            $name = match ($definition['name']) {
                '{session}' => (string) config('session.cookie', 'laravel_session'),
                '{consent_cookie}' => (string) config('filament-lgpd.cookie.name', CookieConsent::VISITOR_COOKIE),
                default => $definition['name'],
            };

            $duration = $definition['duration'] === '{session_duration}'
                ? config('session.lifetime', 120).' min'
                : $definition['duration'];

            $definitionClass::query()->updateOrCreate(
                [
                    'cookie_category_id' => $category->getKey(),
                    'name' => $name,
                ],
                [
                    'provider' => $definition['provider'],
                    'duration' => $duration,
                    'purpose' => $definition['purpose'],
                    'is_first_party' => $definition['is_first_party'],
                    'sort_order' => $definition['sort_order'],
                    'cookie_category_id' => $category->getKey(),
                    'name' => $name,
                ],
            );
        }

        if (! $bannerClass::query()->published()->exists()) {
            $banner = $bannerClass::query()->create(config('filament-lgpd.catalog.banner', []));
            $banner->publish();
        }

        $this->info('The LGPD cookie catalog is ready.');

        return self::SUCCESS;
    }
}
