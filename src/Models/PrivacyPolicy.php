<?php

namespace Guiibraun\FilamentLgpd\Models;

use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property array<string, mixed> $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['body'])]
class PrivacyPolicy extends Model implements HasRichContent
{
    public const CACHE_KEY = 'privacy-policy.public';

    use InteractsWithRichContent;

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.privacy_policy', self::CACHE_KEY)));
        static::deleted(fn (): bool => Cache::forget((string) config('filament-lgpd.cache.privacy_policy', self::CACHE_KEY)));
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('body')->json();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'body' => 'array',
        ];
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }
}
