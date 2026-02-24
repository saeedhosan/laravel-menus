<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class LeadListExtraMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Lead Notes',
                'slug' => 'lead-list.notes',
            ],
        ];
    }
}
