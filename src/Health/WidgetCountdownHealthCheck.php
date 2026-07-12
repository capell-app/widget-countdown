<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Health;

use Capell\LayoutBuilder\Health\AbstractWidgetExtensionHealthCheck;
use Capell\WidgetCountdown\Filament\CountdownWidget;

final class WidgetCountdownHealthCheck extends AbstractWidgetExtensionHealthCheck
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^0.0';
    }

    protected static function definitionKey(): string
    {
        return 'capell-app.countdown';
    }

    protected static function filamentWidget(): string
    {
        return CountdownWidget::class;
    }

    protected static function fallbackView(): string
    {
        return 'capell-widget-countdown::widget';
    }

    protected static function resourceGroups(): array
    {
        return ['capell-app.widget-countdown'];
    }
}
