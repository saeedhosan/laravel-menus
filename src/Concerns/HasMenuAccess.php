<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Concerns;

use BackedEnum;

trait HasMenuAccess
{
    /**
     * Build an access array for Gate checks.
     *
     * @return array<int, string>
     */
    protected function access(string|BackedEnum ...$permissions): array
    {
        return array_values(array_map($this->resolveEnum(...), $permissions));
    }

    private function resolveEnum(string|BackedEnum $value): string
    {
        return $value instanceof BackedEnum ? (string) $value->value : $value;
    }
}
