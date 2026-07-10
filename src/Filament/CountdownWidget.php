<?php

declare(strict_types=1);

namespace Capell\WidgetCountdown\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

final class CountdownWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.countdown';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-countdown::widget.label'))->schema([TextInput::make('title')->required()->maxLength(120), TextInput::make('timezone')->required()->rule('timezone')->default('UTC'), DateTimePicker::make('targetUtc')->timezone('UTC')->seconds(false)->required()->dehydrateStateUsing(static fn (mixed $state): ?string => is_string($state) ? CarbonImmutable::parse($state, 'UTC')->utc()->format(DATE_ATOM) : null), TextInput::make('expiredLabel')->required()->maxLength(120), TextInput::make('ctaLabel')->maxLength(80), TextInput::make('ctaUrl')->maxLength(2048)]);
    }
}
