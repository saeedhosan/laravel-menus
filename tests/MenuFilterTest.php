<?php

declare(strict_types=1);

use SaeedHosan\Menus\MenuFilter;

it('appends items when no filter is set', function () {
    $filter = new MenuFilter('test');

    $items = [
        ['slug' => 'one'],
    ];
    $incoming = [
        ['slug' => 'two'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result)->pluck('slug')->values()->all())
        ->toBe(['one', 'two']);
});

it('filters incoming items with a callback', function () {
    $filter = (new MenuFilter('test'))
        ->filter(fn (array $item) => $item['slug'] !== 'skip');

    $items = [
        ['slug' => 'one'],
    ];
    $incoming = [
        ['slug' => 'keep'],
        ['slug' => 'skip'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result)->pluck('slug')->values()->all())
        ->toBe(['one', 'keep']);
});

it('inserts before a target slug', function () {
    $filter = (new MenuFilter('test'))->before('target');

    $items = [
        ['slug' => 'first'],
        ['slug' => 'target'],
        ['slug' => 'last'],
    ];
    $incoming = [
        ['slug' => 'inserted'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result)->pluck('slug')->values()->all())
        ->toBe(['first', 'inserted', 'target', 'last']);
});

it('inserts after a target slug', function () {
    $filter = (new MenuFilter('test'))->after('target');

    $items = [
        ['slug' => 'first'],
        ['slug' => 'target'],
        ['slug' => 'last'],
    ];
    $incoming = [
        ['slug' => 'inserted'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result)->pluck('slug')->values()->all())
        ->toBe(['first', 'target', 'inserted', 'last']);
});

it('inserts into submenu when whereSlug matches', function () {
    $filter = (new MenuFilter('test'))->whereSlug('billing');

    $items = [
        [
            'slug' => 'billing',
            'submenu' => [
                ['slug' => 'existing'],
            ],
        ],
    ];
    $incoming = [
        ['slug' => 'plans'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result[0]['submenu'])->pluck('slug')->values()->all())
        ->toBe(['existing', 'plans']);
});

it('adds submenu items to a nested target', function () {
    $filter = (new MenuFilter('test'))->whereSlug('settings.security');

    $items = [
        [
            'slug' => 'settings',
            'submenu' => [
                [
                    'slug' => 'settings.security',
                    'submenu' => [
                        ['slug' => 'logs'],
                    ],
                ],
            ],
        ],
    ];
    $incoming = [
        ['slug' => 'tools'],
    ];

    $result = $filter->apply($items, $incoming);
    $security = $result[0]['submenu'][0];

    expect(collect($security['submenu'])->pluck('slug')->values()->all())
        ->toBe(['logs', 'tools']);
});

it('falls back to append when target slug does not exist', function () {
    $filter = (new MenuFilter('test'))->after('missing');

    $items = [
        ['slug' => 'one'],
    ];
    $incoming = [
        ['slug' => 'two'],
    ];

    $result = $filter->apply($items, $incoming);

    expect(collect($result)->pluck('slug')->values()->all())
        ->toBe(['one', 'two']);
});
