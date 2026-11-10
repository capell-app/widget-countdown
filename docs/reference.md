# Countdown Widget reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/widget-countdown`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Countdown Widget is an **Available**, **No schema impact** Capell package in the **Capell Essentials** product group. It ships as `capell-app/widget-countdown` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-countdown`
- Namespace: `Capell\WidgetCountdown`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\WidgetCountdown\Providers\WidgetCountdownServiceProvider`

### Filament classes

- `CountdownWidget`

### Data objects

- `CountdownInputData`
- `CountdownRenderData`

### Manifest contributions

- `asset: Capell\WidgetCountdown\WidgetCountdownContribution`
- `content-widget: Capell\WidgetCountdown\WidgetCountdownContribution`

### Health checks

- `Capell\WidgetCountdown\Health\WidgetCountdownHealthCheck`

### Blade views

- `packages/widget-countdown/resources/views/widget.blade.php`

### Cache tags

- `widget-countdown`


## Data Model

This package has no schema impact. It extends Capell through `asset` contributions and `content-widget` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
