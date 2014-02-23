<?php

use Keevitaja\Keeper\Models\User;
use Keevitaja\Keeper\Models\Role;
use Keevitaja\Keeper\Models\Permission;

class KeeperTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		Artisan::call('migrate');

		User::create([
			'email' => 'kala@kala.ee',
			'password' => Hash::make('1234')
		]);

		User::create([
			'email' => 'test@kala.ee',
			'password' => Hash::make('1234')
		]);

		Role::create(['name' => 'admin']);
		Role::create(['name' => 'moderator']);
		Role::create(['name' => 'client']);

		Permission::create(['name' => 'can_dance']);
		Permission::create(['name' => 'can_jump']);
		Permission::create(['name' => 'can_drive']);
	}

	public function test_users_have_no_roles()
	{
		$this->assertFalse(Keeper::hasRole(1, 'admin'));
		$this->assertFalse(Keeper::hasRole(1, 'moderator'));
		$this->assertFalse(Keeper::hasRole(2, 'admin'));
		$this->assertFalse(Keeper::hasRole(2, 'moderator'));
	}

	public function test_users_have_roles()
	{
		User::find(1)->roles()->attach(1);
		User::find(2)->roles()->attach(1);

		$this->assertTrue(Keeper::hasRole(1, 'admin'));
		$this->assertFalse(Keeper::hasRole(1, 'moderator'));
	}

	public function test_users_have_permission()
	{
		User::find(1)->permissions()->attach(1);

		$this->assertTrue(Keeper::hasPermission(1, 'can_dance'));
		$this->assertFalse(Keeper::hasPermission(1, 'can_jump'));

		User::find(1)->roles()->attach(1);
		Role::find(1)->permissions()->attach(2);

		$this->assertTrue(Keeper::hasPermission(1, 'can_jump'));
		$this->assertFalse(Keeper::hasPermission(2, 'can_jump'));
	}

	public function test_logged_user_has_role()
	{
		Auth::loginUsingId(1);

		$this->assertFalse(Auth::hasRole('moderator'));

		User::find(1)->roles()->attach(2);

		$this->assertTrue(Auth::hasRole('moderator'));
	}

	public function test_logged_user_has_permission()
	{
		Auth::loginUsingId(1);

		$this->assertFalse(Auth::hasPermission('can_drive'));

		User::find(1)->permissions()->attach(3);

		$this->assertTrue(Auth::hasPermission('can_drive'));
	}

	public function test_cache()
	{
		$this->assertFalse(Keeper::hasRole(1, 'client'));

		User::find(1)->roles()->attach(3);

		Config::set('keeper::cache', true);

		$this->assertTrue(Keeper::hasRole(1, 'client'));

		User::find(1)->roles()->detach(3);

		$this->assertTrue(Keeper::hasRole(1, 'client'));

		Cache::flush();

		$this->assertFalse(Keeper::hasRole(1, 'client'));
	}
}