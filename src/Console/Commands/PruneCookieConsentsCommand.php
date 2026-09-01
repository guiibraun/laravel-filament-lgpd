<?php

namespace Guiibraun\FilamentLgpd\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Guiibraun\FilamentLgpd\Models\CookieConsent;

#[Signature('cookies:prune-consents')]
#[Description('Remove Cookie Preference Records older than the retention period')]
class PruneCookieConsentsCommand extends Command
{
    public function handle(): int
    {
        /** @var class-string<CookieConsent> $consentClass */
        $consentClass = (string) config('filament-lgpd.models.cookie_consent', CookieConsent::class);

        $deleted = $consentClass::query()
            ->where('created_at', '<', now()->subYears((int) config('filament-lgpd.retention_years', CookieConsent::RETENTION_YEARS)))
            ->delete();

        $this->info("Deleted {$deleted} cookie preference records.");

        return self::SUCCESS;
    }
}
