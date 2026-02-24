<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Concerns;

trait HasMenuBadge
{
    /**
     * Get menu badge with primary color
     *
     * @return array<int, string>
     */
    public function badgeBeta(string $name = 'Beta'): array
    {
        return [$name, 'badge-light-primary'];
    }

    /**
     * Get menu badge with color
     *
     * @return array<int, string>
     */
    public function badgeNew(string $name = 'New'): array
    {
        return [$name, 'badge-light-success'];
    }

    /**
     * Get menu badge with color
     *
     * @return array<int, string>
     */
    public function badgeSoon(string $name = 'Soon'): array
    {
        return [$name, 'badge-light'];
    }

    /**
     * Get menu badge with color
     *
     * @return array<int, string>
     */
    public function badgeInfo(string $name = 'info'): array
    {
        return [$name, 'badge-light-info'];
    }
}
