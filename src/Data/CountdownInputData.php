<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Data;

use Spatie\LaravelData\Data;

final class CountdownInputData extends Data
{
    public function __construct(public string $title, public string $timezone, public string $targetUtc, public string $expiredLabel, public ?string $ctaLabel = null, public ?string $ctaUrl = null) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['title' => ['required', 'string', 'max:120'], 'timezone' => ['required', 'timezone'], 'targetUtc' => ['required', 'date_format:Y-m-d\TH:i:sP'], 'expiredLabel' => ['required', 'string', 'max:120'], 'ctaLabel' => ['nullable', 'string', 'max:80'], 'ctaUrl' => ['nullable', 'string', 'max:2048', 'regex:/^(?:\/(?!\/)|https?:\/\/)/i']];
    }
}
