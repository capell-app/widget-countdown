<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Actions;

use Carbon\CarbonImmutable;
use DateTimeZone;
use Exception;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

/**
 * Converts between the wall-clock time an editor types in the chosen timezone and the UTC instant stored
 * and rendered publicly, so summer-time events are not an hour out.
 */
final class ConvertCountdownTargetTimezoneAction
{
    use AsFake;
    use AsObject;

    /**
     * Interpret a local wall-clock time in the given timezone and return the UTC instant as an ATOM string.
     */
    public function handle(string $localTime, string $timezone): string
    {
        return CarbonImmutable::parse($localTime, $this->validTimezone($timezone))->utc()->format(DATE_ATOM);
    }

    /**
     * Show a stored UTC instant as the local wall-clock time in the given timezone.
     */
    public function toLocal(string $utcTime, string $timezone): string
    {
        return CarbonImmutable::parse($utcTime, 'UTC')->setTimezone($this->validTimezone($timezone))->format('Y-m-d H:i:s');
    }

    private function validTimezone(string $timezone): string
    {
        try {
            new DateTimeZone($timezone);

            return $timezone;
        } catch (Exception) {
            return 'UTC';
        }
    }
}
