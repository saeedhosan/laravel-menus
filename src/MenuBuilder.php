<?php

declare(strict_types=1);

namespace SaeedHosan\Menus;

use SaeedHosan\Menus\Concerns\HasMenuAccess;
use SaeedHosan\Menus\Concerns\HasMenuBadge;

/**
 * @phpstan-consistent-constructor
 */
abstract class MenuBuilder
{
    use HasMenuAccess;
    use HasMenuBadge;

    /**
     * Get the menu name
     */
    public function name(): string
    {
        return static::class;
    }

    /**
     * Create a new instance
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Get the menu key
     */
    public function renderable(): bool
    {
        return true;
    }

    /**
     * Get the menu items
     *
     * @return list<array<string, mixed>>
     */
    abstract public function items(): array;

    /**
     * Get the menu items
     *
     * @return list<array<string, mixed>>
     */
    public function getItems(): array
    {
        $items = $this->renderable() ? $this->items() : [];

        return $items;
    }
}
