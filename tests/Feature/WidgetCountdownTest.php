<?php

declare(strict_types=1);
use Capell\WidgetCountdown\Data\CountdownInputData;
use Capell\WidgetCountdown\Health\WidgetCountdownHealthCheck;
use Capell\WidgetCountdown\Providers\WidgetCountdownServiceProvider;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->app->register(WidgetCountdownServiceProvider::class);
});
it('registers healthy package surfaces', function (): void {
    expect(WidgetCountdownHealthCheck::passed())->toBeTrue();
});
it('requires an IANA timezone normalized target and safe cta', function (): void {
    expect(fn () => CountdownInputData::validateAndCreate(['title' => 'Launch', 'timezone' => 'Mars/Olympus', 'targetUtc' => 'tomorrow', 'expiredLabel' => 'Live', 'ctaUrl' => 'javascript:alert(1)']))->toThrow(ValidationException::class);
});
