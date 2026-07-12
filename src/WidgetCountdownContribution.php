<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown;

use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;

final class WidgetCountdownContribution implements RegistersExtensionContentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
