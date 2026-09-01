<?php

namespace Guiibraun\FilamentLgpd\Models;

use Guiibraun\FilamentLgpd\Enums\CookieScriptPosition;
use Guiibraun\FilamentLgpd\Enums\CookieScriptSourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cookie_category_id
 * @property string $name
 * @property string $provider
 * @property string $purpose
 * @property CookieScriptPosition $position
 * @property CookieScriptSourceType $source_type
 * @property string|null $src
 * @property string|null $code
 * @property bool $is_active
 * @property int $sort_order
 * @property-read CookieCategory $category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'cookie_category_id',
    'name',
    'provider',
    'purpose',
    'position',
    'source_type',
    'src',
    'code',
    'is_active',
    'sort_order',
])]
class CookieScript extends Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    protected static function booted(): void
    {
        static::saving(function (CookieScript $script): void {
            if ($script->source_type === CookieScriptSourceType::External) {
                $script->code = null;

                return;
            }

            $script->src = null;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => CookieScriptPosition::class,
            'source_type' => CookieScriptSourceType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<CookieCategory, $this>
     */
    public function category(): BelongsTo
    {
        /** @var class-string<CookieCategory> $categoryClass */
        $categoryClass = (string) config('filament-lgpd.models.cookie_category', CookieCategory::class);

        return $this->belongsTo($categoryClass, 'cookie_category_id');
    }

    /**
     * @param  Builder<CookieScript>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param  Builder<CookieScript>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
