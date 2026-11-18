<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\Tests\Support\RenderedDocument;
use Capell\Tests\Support\RenderedHtml;
use Capell\Tests\Support\SchemaStateHarness;
use Capell\WidgetCountdown\Actions\ConvertCountdownTargetTimezoneAction;
use Capell\WidgetCountdown\Data\CountdownInputData;
use Capell\WidgetCountdown\Data\CountdownRenderData;
use Capell\WidgetCountdown\Filament\CountdownWidget;
use Capell\WidgetCountdown\Health\WidgetCountdownHealthCheck;
use Capell\WidgetCountdown\Providers\WidgetCountdownServiceProvider;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\Builder;
use Filament\Schemas\Schema;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

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

it('names widget checks plainly without changing their diagnostic identities', function (): void {
    $results = WidgetCountdownHealthCheck::runDiagnostics();

    expect($results->pluck('id')->all())->toBe([
        'legacy.widget.definition.registration',
        'legacy.filament.block.resolution',
        'legacy.package.fallback.view',
        'legacy.frontend.resource.groups',
    ]);

    foreach ($results as $result) {
        expect($result->label)->toStartWith('Widget Countdown: ')
            ->not->toMatch('/definition|Filament|resolution|resource groups/i');
    }
});

it('announces translated plural boundaries and expiry from rendered public labels', function (string $locale): void {
    resolve(Translator::class)->addLines([
        'widget.day_one' => ':count jour', 'widget.day_other' => ':count jours',
        'widget.day_zero' => ':count jour',
        'widget.hour_one' => ':count heure', 'widget.hour_other' => ':count heures',
        'widget.hour_zero' => ':count heure',
        'widget.minute_one' => ':count minute', 'widget.minute_other' => ':count minutes',
        'widget.minute_zero' => ':count minute',
        'widget.remaining' => ':days, :hours et :minutes restantes',
    ], 'fr', 'capell-widget-countdown');
    app()->setLocale($locale);

    $renderData = new CountdownRenderData('Lancement', 'Europe/Paris', '2026-12-01T12:00:00Z', 'Terminé <test>');
    $html = view('capell-widget-countdown::widget', ['renderData' => $renderData])->render();
    $process = new Process(['node', __DIR__ . '/../Fixtures/localised-countdown.mjs', dirname(__DIR__, 2) . '/resources/dist/widget.js']);
    $document = new RenderedHtml($html);
    $attributes = $document->element('//section[@data-capell-widget-countdown]')->attributes;
    $dataset = [];
    foreach ($attributes as $attribute) {
        if (str_starts_with($attribute->name, 'data-')) {
            $name = lcfirst(str_replace('-', '', ucwords(substr($attribute->name, 5), '-')));
            $dataset[$name] = $attribute->value;
        }
    }

    $process->setInput(json_encode($dataset, JSON_THROW_ON_ERROR));
    $process->run();

    expect($process->getExitCode())->toBe(0, $process->getErrorOutput() . $process->getOutput());
})->with(['fr', 'de']);

it('uses each resolved language for missing or partial French unit translations', function (string $scenario): void {
    $lines = match ($scenario) {
        'missing' => [],
        'other' => ['widget.day_other' => ':count jours', 'widget.hour_other' => ':count heures', 'widget.minute_other' => ':count minutes', 'widget.remaining' => ':days, :hours et :minutes restantes'],
        'partial' => ['widget.day_one' => ':count jour', 'widget.hour_other' => ':count heures', 'widget.remaining' => ':days, :hours et :minutes restantes'],
        default => throw new InvalidArgumentException('Unknown countdown translation scenario.'),
    };
    resolve(Translator::class)->addLines($lines, 'fr', 'capell-widget-countdown');
    app()->setLocale('fr');
    $renderData = new CountdownRenderData('Lancement', 'Europe/Paris', '2026-12-01T12:00:00Z', 'Terminé <test>');
    $document = new RenderedHtml(view('capell-widget-countdown::widget', ['renderData' => $renderData])->render());
    $dataset = [];
    foreach ($document->element('//section[@data-capell-widget-countdown]')->attributes as $attribute) {
        if (str_starts_with($attribute->name, 'data-')) {
            $dataset[lcfirst(str_replace('-', '', ucwords(substr($attribute->name, 5), '-')))] = $attribute->value;
        }
    }

    $process = new Process(['node', __DIR__ . '/../Fixtures/countdown-fallback.mjs', dirname(__DIR__, 2) . '/resources/dist/widget.js', $scenario]);
    $process->setInput(json_encode($dataset, JSON_THROW_ON_ERROR));
    $process->run();

    expect($process->getExitCode())->toBe(0, $process->getErrorOutput() . $process->getOutput());
})->with(['missing', 'other', 'partial']);

it('converts local time in the chosen timezone to the correct UTC instant across the summer-time boundary', function (string $local, string $timezone, string $expectedUtc): void {
    expect(ConvertCountdownTargetTimezoneAction::run($local, $timezone))->toBe($expectedUtc);
})->with([
    'BST in July' => ['2026-07-01 18:00:00', 'Europe/London', '2026-07-01T17:00:00+00:00'],
    'GMT in December' => ['2026-12-01 18:00:00', 'Europe/London', '2026-12-01T18:00:00+00:00'],
    'last GMT day' => ['2026-03-28 18:00:00', 'Europe/London', '2026-03-28T18:00:00+00:00'],
    'first BST day' => ['2026-03-29 18:00:00', 'Europe/London', '2026-03-29T17:00:00+00:00'],
    'last BST day' => ['2026-10-24 18:00:00', 'Europe/London', '2026-10-24T17:00:00+00:00'],
    'first GMT day' => ['2026-10-25 18:00:00', 'Europe/London', '2026-10-25T18:00:00+00:00'],
    'UTC is unchanged' => ['2026-07-01 18:00:00', 'UTC', '2026-07-01T18:00:00+00:00'],
    'New York in July' => ['2026-07-01 18:00:00', 'America/New_York', '2026-07-01T22:00:00+00:00'],
    'invalid timezone falls back to UTC' => ['2026-07-01 18:00:00', 'Mars/Olympus', '2026-07-01T18:00:00+00:00'],
]);

it('shows a stored UTC instant as local time in the chosen timezone', function (): void {
    $action = ConvertCountdownTargetTimezoneAction::make();

    expect($action->toLocal('2026-07-01T17:00:00+00:00', 'Europe/London'))->toBe('2026-07-01 18:00:00')
        ->and($action->toLocal('2026-12-01T18:00:00+00:00', 'Europe/London'))->toBe('2026-12-01 18:00:00')
        ->and(ConvertCountdownTargetTimezoneAction::run($action->toLocal('2026-07-01T17:00:00+00:00', 'Europe/London'), 'Europe/London'))->toBe('2026-07-01T17:00:00+00:00');
});

it('renders the converted July target as 6pm London time', function (): void {
    $utc = ConvertCountdownTargetTimezoneAction::run('2026-07-01 18:00', 'Europe/London');
    $html = view('capell-widget-countdown::widget', ['renderData' => new CountdownRenderData('Summer', 'Europe/London', $utc, 'Started')])->render();

    expect($html)->toContain('data-target="2026-07-01T17:00:00+00:00"')
        ->and((new RenderedDocument($html))->element('//time')->textContent)->toContain('6:00 PM');
});

it('exposes the target picker with a local-time label and helper through the widget form', function (): void {
    $fields = Schema::make(new SchemaStateHarness)->components([CountdownWidget::make()])->getFlatFields();

    expect($fields)->toHaveKey('targetUtc')
        ->and($fields['targetUtc']->getLabel())->toBe('Event date and time');
});

/**
 * Loads a stored countdown block into the widget form and returns the picker state and a saver.
 *
 * @return array{0: string, 1: Closure(string): string}
 */
function countdownEditorRoundTrip(string $storedUtc): array
{
    $harness = new SchemaStateHarness;
    $schema = Schema::make($harness)
        ->components([Builder::make('content')->blocks([CountdownWidget::make()])])
        ->statePath('data');

    $schema->fill(['content' => [['type' => CountdownWidget::getWidgetName(), 'data' => [
        'title' => 'Open studio weekend',
        'timezone' => 'Europe/London',
        'targetUtc' => $storedUtc,
        'expiredLabel' => 'The studio is open',
    ]]]]);

    /** @var array<string, array{data: array{targetUtc: string}}> $content */
    $content = $harness->data['content'];
    $key = (string) array_key_first($content);

    return [
        $content[$key]['data']['targetUtc'],
        static function (string $edited) use ($harness, $schema, $key): string {
            /** @var array<string, array{data: array{targetUtc: string}}> $content */
            $content = $harness->data['content'];
            $content[$key]['data']['targetUtc'] = $edited;
            $harness->data['content'] = $content;

            /** @var array<string, array{data: array{targetUtc: string}}> $saved */
            $saved = $schema->getState()['content'];

            return array_values($saved)[0]['data']['targetUtc'];
        },
    ];
}

it('hydrates the stored UTC target into the picker as the event local time', function (): void {
    [$shown] = countdownEditorRoundTrip('2030-06-21T09:00:00+00:00');

    expect($shown)->toBe('2030-06-21 10:00');
});

it('saves the edited local time back as the UTC instant, including across summer time', function (): void {
    [$shown, $save] = countdownEditorRoundTrip('2030-12-21T09:00:00+00:00');

    expect($shown)->toBe('2030-12-21 09:00')
        ->and($save('2030-06-21 10:00'))->toBe('2030-06-21T09:00:00+00:00');
});

it('shows and saves the same local time when the application timezone is not UTC', function (): void {
    config(['app.timezone' => 'America/New_York']);

    [$shown, $save] = countdownEditorRoundTrip('2030-06-21T09:00:00+00:00');

    expect($shown)->toBe('2030-06-21 10:00')
        ->and($save($shown))->toBe('2030-06-21T09:00:00+00:00');
});
