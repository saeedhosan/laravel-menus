<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class NewAdminMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Settings',
                'slug' => 'admin.settings',
                'access' => 'admin',
            ],
        ];
    }
}
