<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use SaeedHosan\Menus\MenuServiceProvider;

it('registers the menu service provider', function () {
    expect(app()->getLoadedProviders())
        ->toHaveKey(MenuServiceProvider::class);
});

it('loads config defaults', function () {
    expect(config('laravel-menus.view'))->toBe('laravel-menus::rootmenu');
    expect(config('laravel-menus.access_callback'))
        ->toBe(SaeedHosan\Menus\Access\GateAccessCallback::class);
});

it('loads menu views', function () {
    expect(view()->exists('laravel-menus::rootmenu'))->toBeTrue();
    expect(view()->exists('laravel-menus::submenu'))->toBeTrue();
});

it('registers publishable config and views', function () {
    $app = app();

    $ref = new ReflectionObject($app);
    if ($ref->hasProperty('runningInConsole')) {
        $prop = $ref->getProperty('runningInConsole');
        $prop->setAccessible(true);
        $prop->setValue($app, true);
    }

    (new MenuServiceProvider($app))->boot();

    $configPublishes = ServiceProvider::pathsToPublish(
        MenuServiceProvider::class,
        'laravel-menus-config'
    );

    $viewPublishes = ServiceProvider::pathsToPublish(
        MenuServiceProvider::class,
        'laravel-menus-views'
    );

    expect($configPublishes)->not->toBeEmpty();
    expect($viewPublishes)->not->toBeEmpty();

    $configTargets = collect($configPublishes)->values()->all();
    $viewTargets = collect($viewPublishes)->values()->all();

    expect(collect($configTargets)->contains(fn ($path) => str_ends_with($path, 'config/laravel-menus.php')))
        ->toBeTrue();
    expect(collect($viewTargets)->contains(fn ($path) => str_contains($path, 'views/vendor/laravel-menus')))
        ->toBeTrue();
});

it('applies the configured access callback end-to-end', function () {
    config()->set('laravel-menus.access_callback', new class
    {
        public function __invoke(mixed $access): bool
        {
            return $access === 'allow';
        }
    });

    (new MenuServiceProvider(app()))->boot();

    \SaeedHosan\Menus\Menu::flush();
    \SaeedHosan\Menus\Menu::create(new class extends \SaeedHosan\Menus\MenuBuilder
    {
        public function items(): array
        {
            return [
                [
                    'name' => 'Allowed',
                    'slug' => 'allowed',
                    'access' => 'allow',
                ],
                [
                    'name' => 'Denied',
                    'slug' => 'denied',
                    'access' => 'deny',
                ],
            ];
        }
    });

    $items = \SaeedHosan\Menus\Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['allowed']);
});
