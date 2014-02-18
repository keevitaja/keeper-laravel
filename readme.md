# Keeper - Laravel 4.1 Guard extension

Adds role and permission functionality to Laravel 4.1 Guard class

## Install

Install this package using composer

	"require": {
	    "laravel/framework": "4.1.*",
	    "keevitaja/keeper": "dev-master"
	},

and run 

	composer update

Change Auth driver in app/config/auth.php

	'driver' => 'keeper',

Add service provider to app/config/app.php

	'Keevitaja\Keeper\KeeperServiceProvider'

Add facade to app/config/app.php

	'Keeper' => 'Keevitaja\Keeper\Facades\Keeper'

Migrate tables

	php artisan migrate --package=keevitaja/keeper

Following tables are created:

	users: 			id, email, password, created_at, updated_at
	roles: 			id, name, created_at, updated_at
	permissions:	id, name, created_at, updated_at

You may add as many extra columns to the tables as you see fit.


## Usage

This package is not user/role/permission CRUD because there can be n+1 ways to it. Keeper takes care only the authentication part. As does guard. Though following models are provided with nessesary relationships:

- Keevitaja\Keeper\Models\User
- Keevitaja\Keeper\Models\Role
- Keevitaja\Keeper\Models\Permission

All methods return boolean

	Keeper::hasRole($userId, $roleName)

Determine if user belongs to role

	Keeper::hasPermission($userId, $permissionName)

Determine if user has permission

	Auth::hasRole($roleName)

Determine if logged user belongs to role

	Auth::hasPermission($permissionName)

Determine if logged user has permission

All methods provided by Laravel native Guard class are present and working.

User can belong to a role or have a permission. Permissions can be given through a role as well. 

`::hasRole` checks only, if user belongs to the role. Permissions are not included. Suitable for smaller projects.

`::hasPermission` checks, if user has permission given directly or through a role.

Roles and permission work extremly well with laravel route filter system. See usage example below.

## Managing roles and permissions

users, roles and permission tables have pivot relations. So to add user to role:

	Keevitaja\Keeper\Models\User::find(1)->roles()->attach(3)

where 1 is user ID and 3 is role ID. same goes for permissions.

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

Filter names are role and permission names.

For this examples you need role 

- finance 

and permissions

- invoices.create
- invoices.update
- invoices.destroy

This setup gives following:

- `invoices/show` can be accessed by all users who have `finance` role
- `invoices/create` can be accessed by all users who have `finance` role and `invoices.create` permission
- `invoices/destroy` can be accessed by all users who have `finance` role and `invoices.destroy` permission
- `invoices/update` can be accessed by all users who have `invoices.update` permission

It does not matter, if permission is given to user directly (permission_user pivot) or through a role (permission_role pivot).

## If you like this 

please follow me [@keevitaja](https://twitter.com/keevitaja)