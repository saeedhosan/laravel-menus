<?php

declare(strict_types=1);

use SaeedHosan\Menus\Menu;
use Tests\Fixtures\BillingSubMenu;
use Tests\Fixtures\FilteredChildMenu;
use Tests\Fixtures\LeadListExtraMenu;
use Tests\Fixtures\NewAdminMenu;
use Tests\Fixtures\NonRenderableMenu;
use Tests\Fixtures\RootMenu;

beforeEach(function () {
    Menu::flush();
    Menu::accessCallback(fn () => true);
});

it('builds a root menu without children', function () {
    Menu::create(RootMenu::class);

    $items = Menu::make()->render()->toArray();

    expect($items)->toHaveCount(5);
    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing', 'reports']);
});

it('returns an empty menu when nothing is registered', function () {
    $items = Menu::make()->items();

    expect($items)->toBe([]);
});

it('inserts a child menu after a target slug', function () {
    Menu::create(RootMenu::class);
    Menu::create(NewAdminMenu::class, RootMenu::class)->after('admin.clients');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'admin.settings', 'lead-list', 'billing', 'reports']);
});

it('appends items when an after target is not found', function () {
    Menu::create(RootMenu::class);
    Menu::create(NewAdminMenu::class, RootMenu::class)->after('missing.slug');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing', 'reports', 'admin.settings']);
});

it('inserts a child menu before a submenu item by slug', function () {
    Menu::create(RootMenu::class);
    Menu::create(LeadListExtraMenu::class, RootMenu::class)->before('lead-list.vendors');

    $items = Menu::make()->items();
    $leadList = collect($items)->firstWhere('slug', 'lead-list');

    expect(collect($leadList['submenu'])->pluck('slug')->values()->all())
        ->toBe(['lead-list.index', 'lead-list.notes', 'lead-list.vendors']);
});

it('inserts items into a submenu when whereSlug matches', function () {
    Menu::create(RootMenu::class);
    Menu::create(BillingSubMenu::class, RootMenu::class)->whereSlug('billing');

    $items = Menu::make()->items();
    $billing = collect($items)->firstWhere('slug', 'billing');

    expect($billing['submenu'])->toBeArray();
    expect(collect($billing['submenu'])->pluck('slug')->values()->all())
        ->toBe(['billing.plans']);
});

it('forces submenu insertion via asSubmenu for a target', function () {
    Menu::create(RootMenu::class);
    Menu::create(BillingSubMenu::class, RootMenu::class)->asSubmenu()->whereSlug('billing');

    $items = Menu::make()->items();
    $billing = collect($items)->firstWhere('slug', 'billing');

    expect(collect($billing['submenu'])->pluck('slug')->values()->all())
        ->toBe(['billing.plans']);
});

it('filters menu items by access callback including submenus', function () {
    Menu::create(RootMenu::class);

    Menu::accessCallback(function ($access): bool {
        $allowed = collect(['admin', 'view-leads']);

        if (is_iterable($access)) {
            return $allowed->intersect(collect($access))->isNotEmpty();
        }

        return $allowed->contains($access);
    });

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing']);

    $leadList = collect($items)->firstWhere('slug', 'lead-list');
    expect(collect($leadList['submenu'])->pluck('slug')->values()->all())
        ->toBe(['lead-list.index']);
});

it('filters incoming items using MenuFilter callback', function () {
    Menu::create(RootMenu::class);
    Menu::create(FilteredChildMenu::class, RootMenu::class)
        ->after('dashboard')
        ->filter(fn (array $item) => $item['slug'] !== 'filter.second');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'filter.first', 'admin.clients', 'lead-list', 'billing', 'reports']);
});

it('does not render a menu when renderable is false', function () {
    Menu::create(NonRenderableMenu::class);

    $items = Menu::make()->items();

    expect($items)->toBe([]);
});

it('updates an existing menu to attach to a parent with ordering', function () {
    Menu::create(RootMenu::class);
    Menu::create(FilteredChildMenu::class);

    Menu::update(FilteredChildMenu::class, RootMenu::class)->after('billing');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing', 'filter.first', 'filter.second', 'reports']);
});

it('accepts builder instances directly', function () {
    Menu::create(new RootMenu);
    Menu::create(new NewAdminMenu, RootMenu::class)->after('dashboard');

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.settings', 'admin.clients', 'lead-list', 'billing', 'reports']);
});

it('ignores a child menu when its parent is missing', function () {
    Menu::create(NewAdminMenu::class, 'Missing\\Parent\\Menu');

    $items = Menu::make()->items();

    expect($items)->toBe([]);
});

it('builds multiple root menus when no parent is given', function () {
    Menu::create(RootMenu::class);
    Menu::create(NewAdminMenu::class);

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing', 'reports', 'admin.settings']);
});

it('avoids infinite loops when menus form a cycle', function () {
    Menu::create(RootMenu::class, NewAdminMenu::class);
    Menu::create(NewAdminMenu::class, RootMenu::class);

    $items = Menu::make()->items();

    expect($items)->toBe([]);
});

it('skips inserting child items when the filter removes everything', function () {
    Menu::create(RootMenu::class);
    Menu::create(FilteredChildMenu::class, RootMenu::class)
        ->after('dashboard')
        ->filter(fn () => false);

    $items = Menu::make()->items();

    expect(collect($items)->pluck('slug')->values()->all())
        ->toBe(['dashboard', 'admin.clients', 'lead-list', 'billing', 'reports']);
});
