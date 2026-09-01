<?php

namespace Guiibraun\FilamentLgpd\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Guiibraun\FilamentLgpd\Models\PrivacyPolicy;

/**
 * @mixin PrivacyPolicy
 */
class PrivacyPolicyResource extends JsonResource
{
    /**
     * @return array{body: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'body' => $this->renderRichContent('body'),
        ];
    }
}
