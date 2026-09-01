<?php

namespace Guiibraun\FilamentLgpd\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $headline
 * @property string $body
 * @property array<string, mixed>|null $snapshot
 * @property string|null $snapshot_hash
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['headline', 'body', 'snapshot', 'snapshot_hash', 'published_at'])]
class CookieBannerVersion extends Model
{
    public const PUBLISHED_CACHE_KEY = 'cookie-banner.published';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'snapshot' => null,
        'snapshot_hash' => null,
        'published_at' => null,
    ];

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.cookie_banner', self::PUBLISHED_CACHE_KEY)));
        static::deleted(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.cookie_banner', self::PUBLISHED_CACHE_KEY)));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<CookieBannerVersion>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at');
    }

    /**
     * @return HasMany<CookieConsent, $this>
     */
    public function consents(): HasMany
    {
        /** @var class-string<CookieConsent> $consentClass */
        $consentClass = (string) config('filament-lgpd.models.cookie_consent', CookieConsent::class);

        return $this->hasMany($consentClass, 'cookie_banner_version_id');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function publish(): void
    {
        $snapshot = self::captureCatalog();

        $this->forceFill([
            'snapshot' => $snapshot,
            'snapshot_hash' => hash('sha256', (string) json_encode($snapshot)),
            'published_at' => now(),
        ])->save();
    }

    public static function current(): ?self
    {
        return static::query()->published()->latest('published_at')->latest('id')->first();
    }

    /**
     * @return array{categories: list<array<string, mixed>>}
     */
    public static function captureCatalog(): array
    {
        /** @var class-string<CookieCategory> $categoryClass */
        $categoryClass = (string) config('filament-lgpd.models.cookie_category', CookieCategory::class);

        $categories = $categoryClass::query()
            ->ordered()
            ->with('definitions')
            ->get()
            ->map(fn (CookieCategory $category): array => [
                'slug' => $category->slug,
                'name' => $category->name,
                'description' => $category->description,
                'is_required' => $category->is_required,
                'definitions' => array_values($category->definitions
                    ->map(fn (CookieDefinition $definition): array => [
                        'name' => $definition->name,
                        'provider' => $definition->provider,
                        'duration' => $definition->duration,
                        'purpose' => $definition->purpose,
                        'is_first_party' => $definition->is_first_party,
                    ])
                    ->values()
                    ->all()),
            ])
            ->values()
            ->all();

        return [
            'categories' => array_values($categories),
        ];
    }
}
