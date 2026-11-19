<?php

namespace Guiibraun\FilamentLgpd\Http\Resources;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
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
            'body' => is_array($this->body)
                ? RichContentRenderer::make($this->body)->toHtml()
                : (string) $this->body,
        ];
    }
}
