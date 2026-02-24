<?php

declare(strict_types=1);

namespace SaeedHosan\Menus\Access;

use Illuminate\Support\Facades\Gate;
use UnitEnum;

class GateAccessCallback
{
    /**
     * @param  array<string|UnitEnum>|UnitEnum|string  $access
     */
    public function __invoke(iterable|UnitEnum|string $access): bool
    {
        if (is_array($access)) {
            return Gate::any($access);
        }

        return Gate::allows($access);
    }
}
