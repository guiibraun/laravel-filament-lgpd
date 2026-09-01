<?php

namespace Guiibraun\FilamentLgpd\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Guiibraun\FilamentLgpd\Enums\CookieConsentAction;

/**
 * @property string $id
 * @property string $visitor_id
 * @property int $cookie_banner_version_id
 * @property CookieConsentAction $action
 * @property array<string, bool> $choices
 * @property string $source
 * @property string $locale
 * @property string|null $user_agent
 * @property-read CookieBannerVersion $bannerVersion
 * @property Carbon|null $created_at
 */
#[Fillable([
    'visitor_id',
    'cookie_banner_version_id',
    'action',
    'choices',
    'source',
    'locale',
    'user_agent',
])]
class CookieConsent extends Model
{
    public const UPDATED_AT = null;

    public const VISITOR_COOKIE = 'lgpd_consent';

    public const VISITOR_COOKIE_MINUTES = 60 * 24 * 365;

    public const RETENTION_YEARS = 5;

    public const SOURCE_BANNER = 'banner';

    public const SOURCE_PREFERENCES = 'preferences';

    use HasUuids;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => CookieConsentAction::class,
            'choices' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<CookieBannerVersion, $this>
     */
    public function bannerVersion(): BelongsTo
    {
        /** @var class-string<CookieBannerVersion> $bannerClass */
        $bannerClass = (string) config('filament-lgpd.models.cookie_banner_version', CookieBannerVersion::class);

        return $this->belongsTo($bannerClass, 'cookie_banner_version_id');
    }

    public static function latestForVisitor(?string $visitorId): ?self
    {
        if (blank($visitorId) || ! Str::isUuid($visitorId)) {
            return null;
        }

        return static::query()
            ->where('visitor_id', $visitorId)
            ->latest('created_at')
            ->latest('id')
            ->first();
    }
}
