<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class SecurityToolsMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Tools',
                'slug' => 'settings.security.tools',
            ],
        ];
    }
}
