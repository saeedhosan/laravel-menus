<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class SettingsChildMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Notifications',
                'slug' => 'settings.notifications',
            ],
        ];
    }
}
