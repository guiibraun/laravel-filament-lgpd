<?php

namespace Guiibraun\FilamentLgpd\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Guiibraun\FilamentLgpd\Http\Resources\PrivacyPolicyResource;
use Guiibraun\FilamentLgpd\Models\PrivacyPolicy;

class PrivacyController
{
    public function __invoke(): Response
    {
        $modelClass = $this->privacyPolicyClass();
        $privacy = Cache::rememberForever(
            (string) config('filament-lgpd.cache.privacy_policy', PrivacyPolicy::CACHE_KEY),
            function () use ($modelClass): array {
                $policy = $modelClass::current();

                abort_unless($policy instanceof PrivacyPolicy && filled($policy->body), 404);

                return PrivacyPolicyResource::make($policy)->resolve();
            },
        );

        return Inertia::render((string) config('filament-lgpd.inertia.privacy_component', 'Privacy'), [
            'breadcrumb' => $this->breadcrumbs(
                (string) config('filament-lgpd.inertia.privacy_label', 'Privacidade'),
            ),
            'privacy' => $privacy,
        ]);
    }

    /**
     * @return class-string<PrivacyPolicy>
     */
    private function privacyPolicyClass(): string
    {
        $modelClass = config('filament-lgpd.models.privacy_policy', PrivacyPolicy::class);

        return is_string($modelClass) && is_a($modelClass, PrivacyPolicy::class, true)
            ? $modelClass
            : PrivacyPolicy::class;
    }

    /**
     * @return list<array{label: string, url: string|null}>
     */
    private function breadcrumbs(string $currentLabel): array
    {
        return [
            [
                'label' => (string) config('filament-lgpd.inertia.home_label', 'Início'),
                'url' => config('filament-lgpd.inertia.home_url', '/'),
            ],
            [
                'label' => $currentLabel,
                'url' => null,
            ],
        ];
    }
}
