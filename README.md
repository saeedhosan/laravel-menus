# Laravel Menus

Simple, flexible menu builder for Laravel.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

```bash
composer require saeedhosan/laravel-menus
```

The service provider is auto-discovered. To publish the config and views:

```bash
php artisan vendor:publish --tag=laravel-menus-config
php artisan vendor:publish --tag=laravel-menus-views
```

## Creating a Menu

Create a class that extends `MenuBuilder` and define your items:

```php
use SaeedHosan\Menus\MenuBuilder;

class AdminMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'slug' => 'admin.dashboard',
                'link' => route('admin.dashboard'),
                'icon' => '<i class="ki-outline ki-home"></i>',
                'active' => request()->routeIs('admin.dashboard'),
            ],
            [
                'name' => 'Users',
                'slug' => 'admin.users',
                'link' => route('admin.users.index'),
                'active' => request()->routeIs('admin.users.*'),
                'access' => 'manage-users',
            ],
        ];
    }
}
```

### Item Properties

| Key | Type | Description |
|-----|------|-------------|
| `name` | `string` | Display label |
| `slug` | `string` | Unique identifier used for positioning |
| `link` | `string` | URL (optional) |
| `icon` | `string` | Icon HTML (defaults to a bullet dot) |
| `active` | `bool` | Marks item as active |
| `access` | `string\|array` | Permission(s) checked via access callback |
| `badge` | `string\|array` | Badge text, or `['text', 'css-class']` |
| `separator` | `bool` | Renders as a section heading |
| `submenu` | `array` | Nested menu items |

## Registering Menus

Register your menus in a service provider:

```php
use SaeedHosan\Menus\Menu;

// Root menu
Menu::create(AdminMenu::class);

// Child menu — items appended to AdminMenu
Menu::create(ReportsMenu::class, AdminMenu::class);
```

## Positioning Child Items

Control where child items are placed using the fluent filter API:

```php
// Insert after a specific slug
Menu::create(ReportsMenu::class, AdminMenu::class)->after('admin.users');

// Insert before a specific slug
Menu::create(ReportsMenu::class, AdminMenu::class)->before('admin.users');

// Insert into a specific item's submenu
Menu::create(ReportsMenu::class, AdminMenu::class)->whereSlug('admin.settings');

// Force as submenu of matched item
Menu::create(ReportsMenu::class, AdminMenu::class)->asSubmenu()->whereSlug('admin.settings');

// Filter which items get merged
Menu::create(ReportsMenu::class, AdminMenu::class)->filter(fn ($item) => $item['slug'] !== 'hidden');
```

## Updating a Menu's Parent

Move an existing menu registration to a different parent:

```php
Menu::update(ReportsMenu::class, NewParentMenu::class)->after('some.slug');
```

## Rendering

### As Array

```php
$items = Menu::make()->items();

return view('layouts.app', ['items' => $items]);
```

### As HTML

The package ships with Blade views compatible with Metronic/Bootstrap sidebar menus:

```php
{!! Menu::make()->toHtml() !!}
```

You can customize the views after publishing them:

```bash
php artisan vendor:publish --tag=laravel-menus-views
```

Views are published to `resources/views/vendor/laravel-menus`.

## Access Control

Items with an `access` key are filtered through a configurable callback. The default uses Laravel's `Gate`:

```php
// config/laravel-menus.php
'access_callback' => \SaeedHosan\Menus\Access\GateAccessCallback::class,
```

Define permissions on your items:

```php
[
    'name' => 'Reports',
    'slug' => 'reports',
    'access' => 'view-reports',        // single permission
    // 'access' => ['view-reports', 'admin'], // multiple permissions
]
```

To use a custom callback, create an invokable class:

```php
class MyAccessCallback
{
    public function __invoke(string|array $permissions): bool
    {
        // your logic
    }
}
```

## Renderable Condition

Control whether a menu renders at all by overriding `renderable()`:

```php
class AdminMenu extends MenuBuilder
{
    public function renderable(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function items(): array
    {
        return [ /* ... */ ];
    }
}
```

## Badges

`MenuBuilder` provides badge helpers you can use in your items:

```php
class AdminMenu extends MenuBuilder
{
    public function items(): array
    {
        return [
            [
                'name' => 'Feature',
                'slug' => 'feature',
                'badge' => $this->badgeNew(),    // "New" green badge
                // 'badge' => $this->badgeBeta(),  // "Beta" primary badge
                // 'badge' => $this->badgeSoon(),  // "Soon" light badge
                // 'badge' => $this->badgeInfo(),  // "info" info badge
            ],
        ];
    }
}
```

## Testing

```bash
composer test
```

## License

MIT