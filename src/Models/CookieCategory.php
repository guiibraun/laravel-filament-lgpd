<?php

namespace Guiibraun\FilamentLgpd\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property bool $is_required
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['slug', 'name', 'description', 'is_required', 'sort_order'])]
class CookieCategory extends Model
{
    public const NECESSARY = 'necessary';

    public const ANALYTICS = 'analytics';

    public const MARKETING = 'marketing';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_required' => false,
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<CookieDefinition, $this>
     */
    public function definitions(): HasMany
    {
        /** @var class-string<CookieDefinition> $definitionClass */
        $definitionClass = (string) config('filament-lgpd.models.cookie_definition', CookieDefinition::class);

        return $this->hasMany($definitionClass, 'cookie_category_id')->ordered();
    }

    /**
     * @param  Builder<CookieCategory>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
