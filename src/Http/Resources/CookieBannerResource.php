<?php

namespace Guiibraun\FilamentLgpd\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;

/**
 * @mixin CookieBannerVersion
 */
class CookieBannerResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, headline: string, body: string, categories: list<array<string, mixed>>}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'headline' => $this->headline,
            'body' => $this->body,
            'categories' => $this->snapshot['categories'] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function current(): ?array
    {
        $cached = Cache::rememberForever(
            (string) config('filament-lgpd.cache.cookie_banner', CookieBannerVersion::PUBLISHED_CACHE_KEY),
            function (): array {
                /** @var class-string<CookieBannerVersion> $bannerClass */
                $bannerClass = (string) config('filament-lgpd.models.cookie_banner_version', CookieBannerVersion::class);

                $banner = $bannerClass::current();

                return $banner instanceof CookieBannerVersion
                    ? self::make($banner)->resolve()
                    : [];
            },
        );

        return $cached === [] ? null : $cached;
    }
}
