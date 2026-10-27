<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Data;

use Illuminate\Support\Facades\Lang;
use Spatie\LaravelData\Data;

final class CountdownRenderData extends Data
{
    public function __construct(public string $title, public string $timezone, public string $targetUtc, public string $expiredLabel, public ?string $ctaLabel = null, public ?string $ctaUrl = null) {}

    /**
     * @return array<string, list<array{locale: string, forms: array<string, string>}>>
     */
    public function unitLabels(): array
    {
        $labels = [];
        $locales = array_unique([app()->getLocale(), Lang::getFallback()]);

        foreach (['day', 'hour', 'minute'] as $unit) {
            $labels[$unit] = [];
            foreach ($locales as $locale) {
                $forms = [];
                foreach (['zero', 'one', 'two', 'few', 'many', 'other'] as $category) {
                    $key = 'capell-widget-countdown::widget.' . $unit . '_' . $category;
                    if (Lang::hasForLocale($key, $locale)) {
                        // One literal family per unit keeps every plural form statically auditable.
                        $forms[$category] = (string) match ($unit) {
                            'day' => Lang::get('capell-widget-countdown::widget.day_' . $category, [], $locale, false),
                            'hour' => Lang::get('capell-widget-countdown::widget.hour_' . $category, [], $locale, false),
                            'minute' => Lang::get('capell-widget-countdown::widget.minute_' . $category, [], $locale, false),
                        };
                    }
                }

                if ($forms !== []) {
                    $labels[$unit][] = ['locale' => str_replace('_', '-', $locale), 'forms' => $forms];
                }
            }
        }

        return $labels;
    }
}
