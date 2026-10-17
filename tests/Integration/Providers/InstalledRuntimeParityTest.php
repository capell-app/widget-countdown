<?php

declare(strict_types=1);

use Capell\Core\Actions\InstallPackageAction;
use Capell\Core\Actions\RuntimeRefresh\RefreshInstalledPackageRuntimeAction;
use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Support\Assets\FrontendResourceRegistry;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistry;
use Capell\Tests\Support\InstalledRuntimeParity;
use Capell\Tests\Support\InstalledRuntimeState;
use Capell\Tests\Support\InstalledRuntimeTestCase;

it('matches fresh installed boot after in-process installation and repeated refresh', function (): void {
    InstalledRuntimeParity::assertPackage('widget-countdown');
});

it('makes the widget available only after installation to an existing catalogue consumer', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('widget-countdown'), false, sourceRoot: dirname(__DIR__, 3));
        $catalogue = $app->make(WidgetExtensionRegistry::class);
        $resources = $app->make(FrontendResourceRegistry::class);
        expect($catalogue->definition('capell-app.countdown'))->toBeNull()
            ->and($resources->get('capell-app.widget-countdown'))->toBeNull();

        $package = CapellCore::getPackage('capell-app/widget-countdown');
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        InstallPackageAction::run($package);
        RefreshInstalledPackageRuntimeAction::run($package);
        RefreshInstalledPackageRuntimeAction::run($package);

        expect($catalogue->definition('capell-app.countdown')?->packageName)->toBe('capell-app/widget-countdown')
            ->and($resources->get('capell-app.widget-countdown')?->resources)->toHaveCount(2);
    });
});
