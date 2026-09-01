<?php

namespace Guiibraun\FilamentLgpd\Models;

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
 * @property string $duration
 * @property string $purpose
 * @property bool $is_first_party
 * @property int $sort_order
 * @property-read CookieCategory $category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['cookie_category_id', 'name', 'provider', 'duration', 'purpose', 'is_first_party', 'sort_order'])]
class CookieDefinition extends Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_first_party' => true,
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_first_party' => 'boolean',
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
     * @param  Builder<CookieDefinition>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
