<?php

namespace Guiibraun\FilamentLgpd\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Guiibraun\FilamentLgpd\Enums\CookieConsentAction;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;
use Guiibraun\FilamentLgpd\Models\CookieCategory;
use Guiibraun\FilamentLgpd\Models\CookieConsent;

class StoreCookieConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'action' => ['required', Rule::enum(CookieConsentAction::class)],
            'banner_version_id' => ['required', 'integer', Rule::in(array_filter([$this->publishedBanner()?->id]))],
            'source' => ['required', 'string', Rule::in([CookieConsent::SOURCE_BANNER, CookieConsent::SOURCE_PREFERENCES])],
            'choices' => ['required_if:action,'.CookieConsentAction::Customize->value, 'array'],
        ];

        foreach ($this->publishedBanner()?->snapshot['categories'] ?? [] as $category) {
            $slug = (string) ($category['slug'] ?? '');

            if ($slug !== '') {
                $rules["choices.{$slug}"] = ['sometimes', 'boolean'];
            }
        }

        return $rules;
    }

    /**
     * @return array<string, bool>
     */
    public function resolvedChoices(): array
    {
        $categories = $this->publishedBanner()?->snapshot['categories'] ?? [];
        $slugs = [];

        foreach (is_array($categories) ? $categories : [] as $category) {
            $slug = is_array($category) ? ($category['slug'] ?? null) : null;

            if (is_string($slug) && $slug !== '') {
                $slugs[] = $slug;
            }
        }

        $requested = $this->validated('choices') ?? [];
        $action = $this->enum('action', CookieConsentAction::class);

        if (! $action instanceof CookieConsentAction) {
            return [];
        }

        return collect($slugs)
            ->mapWithKeys(function (string $slug) use ($action, $requested): array {
                if ($slug === CookieCategory::NECESSARY) {
                    return [$slug => true];
                }

                return [$slug => match ($action) {
                    CookieConsentAction::AcceptAll => true,
                    CookieConsentAction::RejectNonEssential => false,
                    CookieConsentAction::Customize => (bool) ($requested[$slug] ?? false),
                }];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'action' => 'escolha',
            'banner_version_id' => 'versão do aviso',
            'source' => 'origem',
            'choices' => 'categorias',
        ];
    }

    private function publishedBanner(): ?CookieBannerVersion
    {
        /** @var class-string<CookieBannerVersion> $bannerClass */
        $bannerClass = (string) config('filament-lgpd.models.cookie_banner_version', CookieBannerVersion::class);

        return once(fn (): ?CookieBannerVersion => $bannerClass::current());
    }
}
