<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Data;

use Spatie\LaravelData\Data;

final class CountdownRenderData extends Data
{
    public function __construct(public string $title, public string $timezone, public string $targetUtc, public string $expiredLabel, public ?string $ctaLabel = null, public ?string $ctaUrl = null) {}
}
