# Keeper for Laravel
### role/permission based authentication package using Guard

Keeper  adds roles and permissions functionality to the laravels authentication driver eloquent by extending Guard class. This means, that everything you can do with Auth:: still exists.

Keeper is not a CRUD for user/role/permission database manipulation. There are just too many ways people would like to do it and we would end up with another Sentry with several wierd exceptions which need to be catched. And that's no fun...

Keeper requires atleast Laravel 4 and PHP 5.4

## Todo

- testing

## Install

To install Keeper package using composer, edit composer.json file and run `composer update`.

	"require": {
	    "laravel/framework": "4.1.*",
	    "keevitaja/keeper": "dev-master"
	},

Change Auth driver in app/config/auth.php

	'driver' => 'keeper',

Add service provider to app/config/app.php

	'Keevitaja\Keeper\KeeperServiceProvider'

Add facade to app/config/app.php

	'Keeper' => 'Keevitaja\Keeper\Facades\Keeper'

Migrate tables

	php artisan migrate --package=keevitaja/keeper

After that you have users, roles and permissions tables with pivot tables.

	users:          id, email, password, created_at, updated_at
	roles:          id, name, created_at, updated_at
	permissions:    id, name, created_at, updated_at

To get a hint, how to name roles and permissions, take a look at the Usage example below.

Keeper tables are set up with pivot relations, so for example the users and roles table will have role_user pivot relationship table. Refer to eloquent documentation under [pivot section](http://laravel.com/docs/eloquent#working-with-pivot-tables) on how to make connections between user and role.

You can add as many extra columns to the users, roles and permissions table as you need. You can create new migrations to update created tables or create your own migrations and not migrate from the package migrations. That's up to you. 

## Usage

##### `Keeper::hasRole($userId, $roleName)`

Determine if user has a role - returns true/false

##### `Keeper::hasPermission($userId, $permissionName)`

Determine if user has a permission - returns true/false

##### `Keeper::flushCache()`

Flushes cache, please see cache section below - returns void

##### `Auth::hasRole($roleName)`

Determine if logged user has a role - returns true/false

##### `Auth::hasPermission($permissionName)`

Determine if logged user has a permission  - returns true/false

User can have multiple roles and multiple permissions. Role can have multiple permissions as well. Permissions can be given to user directly or through a role. Keeper is very flexible and suitable for larger and smaller projects. If you need, you can ignore permissions totally and use only roles.

Roles and permission work extremly well with laravel route and filter system. It just makes sense to use them together. See the Usage example below.

## Cache

By default cache is disabled. To activate it, run

	php artisan config:publish keevitaja/keeper

Now you have copy of the config file available in the `app/config/packages/vendor/package` folder where it is safe to modify it.

- `cache` - enables or disables the cache feature
- `cache_id` - identifier for cache key/tag names. no reason to change it
- `cache_expire` - expiration time for cache in minutes
- `cache_tags` - enables or disables the usage of cache tags

Cache Tags are not available with file or database cache driver. As `Keeper::flushCache()` requires cache tags. Flushing feature is only available with the driver which support cache tags. One of them is memcached driver.

`Keeper::flushCache()` flushes only Keeper related cache!

#### Allways clear the cache after database manipulation - users, roles and permissions with pivot tables fall into that category. 

	Keevitaja\Keeper\Models\User::find(1)->roles()->attach(3);
	Keeper::flushCache();

Example above adds user with ID of 1 to a role with ID of 3 and clears cache.

If you must use file driver, then you probably have to flush entire cache or come up with your own solution. Las resort it to not use cache feature.

## Managing roles and permissions

Keeper does not provide CRUD for database manipulation. All model methods are abstracted into traits, so it would be possible to use relations really easy in other Eloquent models in your project. See one example in Cache section.

## Usage example

```php
Route::filter('finance', function()
{
	if ( ! Auth::hasRole('finance')) dd('no finance role');
});

Route::filter('invoices.create', function()
{
	if ( ! Auth::hasPermission('invoices.create')) dd('no create permission');
});

Route::filter('invoices.update', function()
{
	if ( ! Auth::hasPermission('invoices.update')) dd('no update permission');
});

Route::filter('invoices.destroy', function()
{
	if ( ! Auth::hasPermission('invoices.destroy')) dd('no destroy permission');
});

Route::group(['prefix' => 'invoices', 'before' => 'finance'], function()
{
	Route::get('show', 'InvoicesController@show');
	Route::get('create', 'InvoicesController@create')->before('invoices.create');
	Route::get('destroy', 'InvoicesController@destroy')->before('invoices.destroy');
});

Route::get('invoices/update', 'InvoicesController@update')->before('invoices.update');
```

Filter names in this example are the role and permission names. You can name permissions any way you like, but `controller.permission` seems to make sense. At least for me.

For this example to work you need a role 

- finance 

and permissions

- invoices.create
- invoices.update
- invoices.destroy

These routes and filters give you the following setup:

- `invoices/show` can be accessed by all users who have `finance` role
- `invoices/create` can be accessed by all users who have `finance` role and `invoices.create` permission
- `invoices/destroy` can be accessed by all users who have `finance` role and `invoices.destroy` permission
- `invoices/update` can be accessed by all users who have `invoices.update` permission

It does not matter, if permission is given to user directly (permission_user pivot) or through a role (permission_role pivot).

## If you like this 

please follow me [@keevitaja](https://twitter.com/keevitaja)