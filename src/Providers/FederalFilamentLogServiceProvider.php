<?php

namespace Shieldforce\FederalFilamentLog\Providers;

use Shieldforce\CheckoutPayment\CheckoutPaymentServiceProvider as BaseProvider;

class FederalFilamentLogServiceProvider extends BaseProvider
{
    public function boot(): void
    {
        parent::boot();

        $viewsPath = __DIR__ . '/../../resources/views';

        if (is_dir($viewsPath)) {
            // Carrega views do plugin
            $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'federal-filament-log');

            $this->publishes([
                $viewsPath => resource_path('views/vendor/federal-filament-log'),
            ], 'views');
        }
    }
}

