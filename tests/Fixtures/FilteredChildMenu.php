<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class FilteredChildMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'First',
                'slug' => 'filter.first',
            ],
            [
                'name' => 'Second',
                'slug' => 'filter.second',
            ],
        ];
    }
}
