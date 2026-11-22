# Countdown Widget

<!-- prettier-ignore-start -->

Six weeks before the wedding, guests start asking how long is left. Put a countdown on the invitation page and they'll see the days, hours, minutes and seconds ticking down below the date written out in full. The same works for an open day, a launch or an entry deadline.

Each countdown has a title, a target time, an end message and an optional button with a link. Its colours follow the rest of your site. At zero the digits stay and your message appears beneath them. It needs Layout Builder, the page builder, to go on a page. The live ticking and end message need a small script in the visitor's browser. Without it, they see the written date and the time left when the page was built.

Type the time zone first, such as Europe/London, then the time the event starts there. Summer time is handled for you, so a 6pm wedding in London in July is entered as 6pm, and the written date reads 6:00 PM BST. The time zone box starts as UTC.

Visitors using a screen reader hear the time left once when the page loads and again when it ends.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/marketplace/extensions/widget-countdown).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/marketplace/extensions/widget-countdown).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.79`, `capell-app/frontend ^1.0.79`, `capell-app/layout-builder ^1.0`, `lorisleiva/laravel-actions ^2.8`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-countdown:@beta`.

## Quick Start

1. Install the package: `composer require capell-app/widget-countdown:@beta`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Countdown**.

## For Developers

Typed input and render boundaries normalise time handling and reject invalid timezone or unsafe call-to-action values before rendering.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.79`, `capell-app/frontend ^1.0.79`, `capell-app/layout-builder ^1.0`.
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
