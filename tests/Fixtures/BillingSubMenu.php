<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class BillingSubMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Plans',
                'slug' => 'billing.plans',
            ],
        ];
    }
}
