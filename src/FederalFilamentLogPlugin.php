<?php

namespace Shieldforce\FederalFilamentLog;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FederalFilamentLogPlugin implements Plugin
{
    public string $labelGroupSidebar = 'Logs';

    public function getId(): string
    {
        return 'federal-filament-log';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->routes(function () {})
            ->pages([
                \Shieldforce\FederalFilamentLog\Pages\FederalFilamentLogsPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        config()->set('federal-filament-log.sidebar_group', $this->labelGroupSidebar);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function setLabelGroupSidebar(
        string $labelGroupSidebar
    ): static {
        $this->labelGroupSidebar = $labelGroupSidebar;

        return $this;
    }

    public function getLabelGroupSidebar()
    {
        return $this->labelGroupSidebar;
    }
}
