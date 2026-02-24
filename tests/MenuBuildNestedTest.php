<?php

declare(strict_types=1);

use SaeedHosan\Menus\Menu;
use Tests\Fixtures\NestedMenuRoot;
use Tests\Fixtures\RootMenu;
use Tests\Fixtures\SecurityToolsMenu;
use Tests\Fixtures\SettingsChildMenu;

beforeEach(function () {
    Menu::flush();
    Menu::accessCallback(fn () => true);
});

it('inserts before a nested submenu item', function () {
    Menu::create(NestedMenuRoot::class);
    Menu::create(SettingsChildMenu::class, NestedMenuRoot::class)->before('settings.security');

    $items = Menu::make()->items();
    $settings = collect($items)->firstWhere('slug', 'settings');

    expect(collect($settings['submenu'])->pluck('slug')->values()->all())
        ->toBe(['settings.profile', 'settings.notifications', 'settings.security']);
});

it('adds submenu items to a nested target by slug', function () {
    Menu::create(NestedMenuRoot::class);
    Menu::create(SecurityToolsMenu::class, NestedMenuRoot::class)->whereSlug('settings.security');

    $items = Menu::make()->items();
    $settings = collect($items)->firstWhere('slug', 'settings');
    $security = collect($settings['submenu'])->firstWhere('slug', 'settings.security');

    expect(collect($security['submenu'])->pluck('slug')->values()->all())
        ->toBe(['settings.security.logs', 'settings.security.tools']);
});

it('applies child menus with the latest registration closest to the target', function () {
    Menu::create(RootMenu::class);
    Menu::create(SettingsChildMenu::class, RootMenu::class)->after('dashboard');
    Menu::create(SecurityToolsMenu::class, RootMenu::class)->after('dashboard');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe([
            'dashboard',
            'settings.security.tools',
            'settings.notifications',
            'admin.clients',
            'lead-list',
            'billing',
            'reports',
        ]);
});
