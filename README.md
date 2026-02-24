# Laravel Menus

Simple, flexible menu builder for Laravel.

**Install**

```bash
composer require saeedhosan/laravel-menus
```

**Publish (optional)**

```bash
php artisan vendor:publish --tag=laravel-menus-config
php artisan vendor:publish --tag=laravel-menus-views
```

**Build A Menu**

```php
use SaeedHosan\Menus\Menu;
use App\Menus\AdminMenu;

Menu::create(AdminMenu::class);
Menu::make()->render()->toArray();

Menu::make()->toArray(); // or cached items
Menu::make()->toHtml(); // or build html
```

**Add Items To Another Menu**

```php
Menu::create(NewAdminMenu::class, AdminMenu::class)->after('admin.clients');
Menu::create(SystemMenu::class, NewAdminMenu::class)->after('admin.clients');
```

**Update Existing**

```php
Menu::update(SystemMenu::class, NewAdminMenu::class)->after('admin.clients');
```

**Filter Placement**

```php
Menu::create(ExtraMenu::class, AdminMenu::class)->before('lead-list.vendors');
Menu::create(BillingMenu::class, AdminMenu::class)->whereSlug('billing');
Menu::create(BillingMenu::class, AdminMenu::class)->asSubmenu()->whereSlug('billing');
```

**Access Control**

```php
// config/laravel-menus.php
'access_callback' => \SaeedHosan\Menus\Access\GateAccessCallback::class,
```

```php
// per item
[
    'name' => 'Reports',
    'slug' => 'reports',
    'access' => ['view-reports', 'admin'],
]
```

**Render Blade**

```php
return view('any', [
    'items' => Menu::make()->toArray(),
]);
```

**Menu Builder**

```php
use SaeedHosan\Menus\MenuBuilder;

class AdminMenu extends MenuBuilder
{
    public function renderable(): bool
    {
        return true;
    }

    public function items(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'slug' => 'admin.dashboard',
                'link' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
            ],
        ];
    }
}
```

**Renderable condition**

```php
use SaeedHosan\Menus\MenuBuilder;
use Illuminate\Support\Facades\Gate;

class AdminMenu extends MenuBuilder
{
    public function renderable(): bool
    {
        return Gate::has('access admin');
    }

    //....
}
```
