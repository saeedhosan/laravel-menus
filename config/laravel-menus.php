<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Menu access allow callback
    |--------------------------------------------------------------------------
    |
    | This determines whether a menu item is accessible.
    | Provide a callable or an invokable class name (avoid closures for config caching).
    |
    */
    'access_callback' => \SaeedHosan\Menus\Access\GateAccessCallback::class,

    /*
    |--------------------------------------------------------------------------
    | Menu view
    |--------------------------------------------------------------------------
    |
    | The view used when rendering the menu HTML.
    |
    */
    'view' => 'laravel-menus::rootmenu',

];
