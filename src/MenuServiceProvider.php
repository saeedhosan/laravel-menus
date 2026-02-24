<?php

declare(strict_types=1);

namespace SaeedHosan\Menus;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use InvalidArgumentException;

class MenuServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-menus.php', 'laravel-menus');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishMenu();
        $this->registerMenuAccessCallback();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-menus');
    }

    private function publishMenu(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-menus.php' => config_path('laravel-menus.php'),
        ], 'laravel-menus-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-menus'),
        ], 'laravel-menus-views');
    }

    private function registerMenuAccessCallback(): void
    {
        $callback = config('laravel-menus.access_callback');

        if ($callback === null) {
            return;
        }

        // Instantiate (expects __invoke)
        $callback = new $callback;

        if (! is_callable($callback)) {
            throw new InvalidArgumentException(
                'laravel-menus.access_callback class must be invokable.'
            );
        }

        Menu::accessCallback($callback);
    }
}
