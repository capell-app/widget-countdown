# Countdown Widget

<!-- prettier-ignore-start -->

## What This Plugin Adds

Countdown Widget is an **Available**, **No schema impact** Capell package in the **Capell Foundation** product group. It ships as `capell-app/widget-countdown` and extends these surfaces: admin, frontend.

Countdown adds a Layout Builder widget for a scheduled target using an IANA timezone and normalized UTC value. Its public output includes a fallback state and time-change announcements.

Editors set the target, timezone, labels, and optional call to action in Layout Builder, and visitors see the remaining time on the public page.

Evidence: [`src/Filament/CountdownWidget.php`](src/Filament/CountdownWidget.php), [`resources/views/widget.blade.php`](resources/views/widget.blade.php), [`tests/Feature/WidgetCountdownTest.php`](tests/Feature/WidgetCountdownTest.php), [`capell.json`](capell.json).

Status details:

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-countdown`
- Namespace: `Capell\WidgetCountdown`
- Theme key: not applicable

## Why It Matters

**For developers:** Typed input and render boundaries normalize time handling and reject invalid timezone or unsafe call-to-action values before rendering.

**For teams:** Campaign and event teams can publish a clear deadline without hand-calculating visitor-facing timezone differences.

Evidence: [`src/Providers/WidgetCountdownServiceProvider.php`](src/Providers/WidgetCountdownServiceProvider.php), [`src/Filament/CountdownWidget.php`](src/Filament/CountdownWidget.php), [`tests/Feature/WidgetCountdownTest.php`](tests/Feature/WidgetCountdownTest.php), [`resources/views/widget.blade.php`](resources/views/widget.blade.php).

## Screens And Workflow

Screenshot contract: `docs/screenshots.json`.

![Countdown Widget extension card](docs/screenshots/extension-card.svg)

- Countdown Widget extension card (marketplace, required).
- Countdown fields in Layout Builder (admin, optional).
- Countdown public output (frontend, optional).

## Technical Shape

- Service providers: `Capell\WidgetCountdown\Providers\WidgetCountdownServiceProvider`.
- Filament classes: `CountdownWidget`.
- Data objects: `CountdownInputData`, `CountdownRenderData`.
- Manifest contributions: `content-widget: Capell\WidgetCountdown\WidgetCountdownContribution`.
- Health checks: `Capell\WidgetCountdown\Health\WidgetCountdownHealthCheck`.
- Blade views: `packages/widget-countdown/resources/views/widget.blade.php`.
- Cache tags: `widget-countdown`.

## Data Model

This package has no schema impact. It extends Capell through `content-widget` contributions instead of declaring package-owned tables.

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetCountdownContribution`.
- Permissions: none declared in `capell.json`.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-countdown`.
- Commands: none declared.

## Common Pitfalls

- Keep required Capell packages on compatible v4 releases: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-countdown` cache tags.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Quick Start

1. Install the package: `composer require capell-app/widget-countdown`.
2. No package-specific setup command or migrations are declared.
3. Open the package detail or install-intent surface and confirm the Countdown Widget extension card is present.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Screenshot contract](docs/screenshots.json)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).
- Focused tests: `vendor/bin/pest packages/widget-countdown/tests --configuration=phpunit.xml`.

<!-- prettier-ignore-end -->
