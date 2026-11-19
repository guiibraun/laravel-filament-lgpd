<?php

namespace Guiibraun\FilamentLgpd\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property array<string, mixed>|string $body TipTap JSON (Filament) or sanitized HTML
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['body'])]
class PrivacyPolicy extends Model
{
    public const CACHE_KEY = 'privacy-policy.public';

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.privacy_policy', self::CACHE_KEY)));
        static::deleted(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.privacy_policy', self::CACHE_KEY)));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'body' => 'json',
        ];
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }
}
