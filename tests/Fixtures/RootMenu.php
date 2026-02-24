<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class RootMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
            ],
            [
                'name' => 'Clients',
                'slug' => 'admin.clients',
                'access' => 'admin',
            ],
            [
                'name' => 'Lead List',
                'slug' => 'lead-list',
                'submenu' => [
                    [
                        'name' => 'Lead Index',
                        'slug' => 'lead-list.index',
                        'access' => 'view-leads',
                    ],
                    [
                        'name' => 'Lead Vendors',
                        'slug' => 'lead-list.vendors',
                        'access' => 'view-vendors',
                    ],
                ],
            ],
            [
                'name' => 'Billing',
                'slug' => 'billing',
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'access' => 'view-reports',
            ],
        ];
    }
}
