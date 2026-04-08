<?php

namespace AgenticMorf\FluxUILicensing;

use AgenticMorf\FluxUILicensing\Livewire\LicenseDashboard;
use AgenticMorf\FluxUILicensing\Livewire\LicenseManager;
use AgenticMorf\FluxUILicensing\Livewire\LicenseScopeManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class FluxUILicensingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fluxui-licensing.php', 'fluxui-licensing');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fluxui-licensing');

        Livewire::component('fluxui-licensing.license-manager', LicenseManager::class);
        Livewire::component('fluxui-licensing.license-scope-manager', LicenseScopeManager::class);
        Livewire::component('fluxui-licensing.license-dashboard', LicenseDashboard::class);

        if ($this->app->routesAreCached()) {
            return;
        }

        $middleware = config('fluxui-licensing.middleware', ['web', 'auth', 'verified']);

        Route::middleware($middleware)->group(function (): void {
            Route::view(
                config('fluxui-licensing.route', 'settings/licenses'),
                'fluxui-licensing::settings.licenses',
            )->name(config('fluxui-licensing.route_name', 'licensing.index'));

            Route::view(
                config('fluxui-licensing.route', 'settings/licenses').'/scopes',
                'fluxui-licensing::settings.scopes',
            )->name('licensing.scopes');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fluxui-licensing.php' => config_path('fluxui-licensing.php'),
            ], 'fluxui-licensing-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/fluxui-licensing'),
            ], 'fluxui-licensing-views');
        }
    }
}
