<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use SaeedHosan\Menus\MenuBuilder;

class NonRenderableMenu extends MenuBuilder
{
    public function renderable(): bool
    {
        return false;
    }

    public function items(): array
    {
        return [
            [
                'name' => 'Hidden',
                'slug' => 'hidden',
            ],
        ];
    }
}
