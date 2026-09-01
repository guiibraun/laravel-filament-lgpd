<?php

namespace Guiibraun\FilamentLgpd\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Guiibraun\FilamentLgpd\Enums\CookieScriptPosition;
use Guiibraun\FilamentLgpd\Enums\CookieScriptSourceType;

/**
 * @property int $id
 * @property string $headline
 * @property string $body
 * @property array<string, string>|null $colors
 * @property array<string, mixed>|null $snapshot
 * @property string|null $snapshot_hash
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['headline', 'body', 'colors', 'snapshot', 'snapshot_hash', 'published_at'])]
class CookieBannerVersion extends Model
{
    public const PUBLISHED_CACHE_KEY = 'cookie-banner.published';

    /**
     * @var array<string, string>
     */
    public const DEFAULT_COLORS = [
        'background' => '#ffffff',
        'foreground' => '#111827',
        'primary' => '#2563eb',
        'primary_foreground' => '#ffffff',
        'border' => '#e5e7eb',
        'overlay' => '#00000080',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'colors' => null,
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
            'colors' => 'array',
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
        $colors = $this->resolvedColors();
        $snapshot = [
            ...self::captureCatalog(),
            'colors' => $colors,
        ];

        $this->forceFill([
            'colors' => $colors,
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
     * @return array<string, string>
     */
    public static function defaultColors(): array
    {
        $configuredColors = config('filament-lgpd.catalog.banner.colors', self::DEFAULT_COLORS);

        return self::normalizeColors($configuredColors, self::DEFAULT_COLORS);
    }

    /**
     * @return array<string, string>
     */
    public function resolvedColors(): array
    {
        return self::normalizeColors($this->colors, self::defaultColors());
    }

    /**
     * @return array<string, string>
     */
    public function publishedColors(): array
    {
        $snapshotColors = is_array($this->snapshot)
            ? ($this->snapshot['colors'] ?? null)
            : null;

        return self::normalizeColors($snapshotColors, $this->resolvedColors());
    }

    /**
     * @param  mixed  $colors
     * @param  array<string, string>|null  $fallback
     * @return array<string, string>
     */
    public static function normalizeColors(mixed $colors, ?array $fallback = null): array
    {
        $fallbackColors = $fallback ?? self::DEFAULT_COLORS;
        $resolved = [];

        foreach (array_keys(self::DEFAULT_COLORS) as $key) {
            $value = is_array($colors) ? ($colors[$key] ?? null) : null;
            $fallbackValue = $fallbackColors[$key] ?? self::DEFAULT_COLORS[$key];

            $resolved[$key] = self::isValidHexColor($value)
                ? $value
                : (self::isValidHexColor($fallbackValue)
                    ? $fallbackValue
                    : self::DEFAULT_COLORS[$key]);
        }

        return $resolved;
    }

    private static function isValidHexColor(mixed $value): bool
    {
        return is_string($value)
            && preg_match('/\A#(?:(?:[0-9a-f]{3}){1,2}|(?:[0-9a-f]{4}){1,2})\z/i', $value) === 1;
    }

    /**
     * @return array{categories: list<array<string, mixed>>, scripts: list<array<string, mixed>>}
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

        /** @var class-string<CookieScript> $scriptClass */
        $scriptClass = (string) config('filament-lgpd.models.cookie_script', CookieScript::class);

        $scripts = $scriptClass::query()
            ->active()
            ->ordered()
            ->with('category')
            ->get()
            ->filter(fn (CookieScript $script): bool => $script->category !== null)
            ->map(fn (CookieScript $script): array => [
                'id' => $script->getKey(),
                'name' => $script->name,
                'provider' => $script->provider,
                'purpose' => $script->purpose,
                'category' => $script->category->slug,
                'is_required' => $script->category->is_required,
                'position' => $script->position instanceof CookieScriptPosition
                    ? $script->position->value
                    : (string) $script->position,
                'source_type' => $script->source_type instanceof CookieScriptSourceType
                    ? $script->source_type->value
                    : (string) $script->source_type,
                'src' => $script->src,
                'code' => $script->code,
                'sort_order' => $script->sort_order,
            ])
            ->values()
            ->all();

        return [
            'categories' => array_values($categories),
            'scripts' => array_values($scripts),
        ];
    }
}
