<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\Tests\Support\RenderedDocument;
use Capell\WidgetCountdown\Data\CountdownInputData;
use Capell\WidgetCountdown\Data\CountdownRenderData;
use Capell\WidgetCountdown\Health\WidgetCountdownHealthCheck;
use Capell\WidgetCountdown\Providers\WidgetCountdownServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-countdown');
    $this->app->register(WidgetCountdownServiceProvider::class);
});
it('registers healthy package surfaces', function (): void {
    expect(WidgetCountdownHealthCheck::passed())->toBeTrue();
});
it('requires an IANA timezone normalized target and safe cta', function (): void {
    expect(fn (): CountdownInputData => CountdownInputData::validateAndCreate(['title' => 'Launch', 'timezone' => 'Mars/Olympus', 'targetUtc' => 'tomorrow', 'expiredLabel' => 'Live', 'ctaUrl' => 'javascript:alert(1)']))->toThrow(ValidationException::class);
});
it('renders public countdown markup accepted by the public HTML safety action', function (): void {
    $renderData = new CountdownRenderData(
        title: 'Launch',
        timezone: 'Europe/London',
        targetUtc: '2026-12-01T12:00:00+00:00',
        expiredLabel: 'Started',
    );
    CarbonImmutable::setTestNow('2026-11-30T10:57:56+00:00');
    try {
        $html = view('capell-widget-countdown::widget', ['renderData' => $renderData])->render();
    } finally {
        CarbonImmutable::setTestNow();
    }
    $document = new RenderedDocument($html);
    expect($document->element('//h2')->textContent)->toBe('Launch')
        ->and($document->element('//b[@data-days]')->textContent)->toBe('1')
        ->and($document->element('//b[@data-hours]')->textContent)->toBe('1')
        ->and($document->element('//b[@data-minutes]')->textContent)->toBe('2')
        ->and($document->element('//b[@data-seconds]')->textContent)->toBe('4')
        ->and($document->element('//time')->getAttribute('datetime'))->toBe($renderData->targetUtc)
        ->and($document->element('//p[@role="status"]')->getAttribute('aria-live'))->toBe('polite');

    expect($html)
        ->toContain('data-capell-widget-countdown')
        ->not->toContain('<b data-days>0</b')
        ->not->toContain('data-capell-countdown');

    AssertPublicHtmlContainsNoAuthoringSurfaceAction::run(new Response(
        '<!doctype html><html><body>' . $html . '</body></html>',
        Response::HTTP_OK,
        ['Content-Type' => 'text/html'],
    ));

    expect(resolve(PublicHtmlSafetyInspector::class)->containsAuthoringSurface(
        '<section data-capell-countdown data-target="2026-12-01T12:00:00+00:00">Countdown</section>',
    ))->toBeTrue();
});
