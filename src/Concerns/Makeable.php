<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Concerns;

trait Makeable
{
    public static function make(): self
    {
        return app(self::class);
    }
}
