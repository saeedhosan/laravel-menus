<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Concerns;

trait MenuAccessableCallback
{
    /**
     * @var callable|null
     */
    protected static $accessFilterCallback = null;

    /**
     * @param  callable(mixed): bool  $callback
     */
    public static function accessCallback(callable $callback): void
    {
        self::$accessFilterCallback = $callback;
    }
}
