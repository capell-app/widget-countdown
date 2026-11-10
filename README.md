# Countdown Widget

<!-- prettier-ignore-start -->

## What It Does

Countdown adds a Layout Builder widget for a scheduled target using an IANA timezone and normalised UTC value. Its public output includes a fallback state and time-change announcements.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-countdown).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-countdown).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-countdown`.

Editors set the target, timezone, labels, and optional call to action in Layout Builder, and visitors see the remaining time on the public page.

## Who It Is For

**For developers:** Typed input and render boundaries normalise time handling and reject invalid timezone or unsafe call-to-action values before rendering.

**For teams:** Campaign and event teams can publish a clear deadline without hand-calculating visitor-facing timezone differences.

## Quick Start

1. Install the package: `composer require capell-app/widget-countdown`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Countdown**.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-countdown` cache tags.

## Screens And Workflow

![Countdown public output](docs/screenshots/countdown-public-output.png)

![Countdown fields in Layout Builder](docs/screenshots/countdown-layout-builder-editor.png)

- Countdown Widget extension card (marketplace).
- Countdown fields in Layout Builder (admin).
- Countdown public output (frontend).
- Countdown fields in Layout Builder with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetCountdownContribution`.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-countdown`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |
| The target time or timezone is refused | The timezone is not an IANA name, or the target is not a full date and time with an offset | Check the Timezone field against the IANA list and that a target date and time is set | Enter a timezone such as `Europe/London` and pick a target date and time |
| The call to action is refused | The link is not a site path starting with `/` or a full `http://` or `https://` address | Check the call-to-action link and that its label is 80 characters or fewer | Use a path such as `/offers` or a full web address |

## Upgrading

No manual steps between minor versions.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Developer reference](docs/reference.md)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md).

<!-- prettier-ignore-end -->
