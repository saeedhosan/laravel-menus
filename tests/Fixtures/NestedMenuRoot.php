<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class NestedMenuRoot extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'submenu' => [
                    [
                        'name' => 'Profile',
                        'slug' => 'settings.profile',
                    ],
                    [
                        'name' => 'Security',
                        'slug' => 'settings.security',
                        'submenu' => [
                            [
                                'name' => 'Logs',
                                'slug' => 'settings.security.logs',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
