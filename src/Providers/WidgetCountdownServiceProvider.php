<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Providers;

use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionCapabilitiesData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Enums\WidgetPresentationCapability;
use Capell\LayoutBuilder\Providers\AbstractFoundationWidgetServiceProvider;
use Capell\WidgetCountdown\Data\CountdownInputData;
use Capell\WidgetCountdown\Data\CountdownRenderData;
use Capell\WidgetCountdown\Filament\CountdownWidget;

final class WidgetCountdownServiceProvider extends AbstractFoundationWidgetServiceProvider
{
    public static string $name = 'capell-widget-countdown';

    public static string $packageName = 'capell-app/widget-countdown';

    protected static function resourceGroup(): string
    {
        return 'capell-app.widget-countdown';
    }

    protected function definition(): WidgetExtensionDefinitionData
    {
        return new WidgetExtensionDefinitionData(key: 'capell-app.countdown', packageName: self::$packageName, stateVersion: 1, filamentWidget: CountdownWidget::class, inputData: CountdownInputData::class, renderData: CountdownRenderData::class, fallbackView: 'capell-widget-countdown::widget', components: ['blade' => 'capell::widgets.capell-app.countdown'], resourceGroups: [self::resourceGroup()], defaultResourceLoadingStrategy: PresentationLoadingStrategy::Visible, capabilities: new WidgetExtensionCapabilitiesData(requiresInstanceIdentity: true, presentationCapabilities: [WidgetPresentationCapability::Width, WidgetPresentationCapability::Alignment, WidgetPresentationCapability::LoadingStrategy]));
    }
}
