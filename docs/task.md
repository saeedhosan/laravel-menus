We need to read the full package files to understand the context
The package is fully placheholder soruces with couple of example to apply
We should lookup example/ and example/app/Useage.php to understand the final outcome.
The must be flexible for user to use it esilay for better developer experience.
The menu builder should work with main menu/sum-menu and access-based rendered.
Menu/Submenu position would by MenuFilter which is now just placehodler,its required to update.
collect: We will use laravel best practice and laravel collec insted of php looping
complete all logic into menu.build method as much as possible while keeping clean code.
For complex filter logic it must move to to MenuFilter with own method to use reference.

Note: Do not make code complex and make sure ist readable any human to simply to understand

final: The menu must be scalable production ready and flexible and realiable package to use.

usage:  

        Menu::create(AdminMenu::class);
        Menu::create(NewAdminMenu::class, AdminMenu::class)->after('admin.clients');
        Menu::create(SystemMenu::class, NewAdminMenu::class)->after('admin.clients');

        //or
        Menu::update(SystemMenu::class, NewAdminMenu::class)->after('admin.clients');