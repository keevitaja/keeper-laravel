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

	public function test_user_1_has_admin_role()
	{
		User::find(1)->roles()->attach(1);

		$this->assertTrue(Keeper::hasRole(1, 'admin'));
	}

	public function test_user_2_has_moderator_role()
	{
		User::find(2)->roles()->attach(2);

		$this->assertTrue(Keeper::hasRole(2, 'moderator'));
	}

	public function test_user_1_has_not_moderator_role()
	{
		$this->assertFalse(Keeper::hasRole(1, 'moderator'));
	}

	public function test_users_have_no_can_jump_permissions()
	{
		$this->assertFalse(Keeper::hasPermission(1, 'can_jump'));
		$this->assertFalse(Keeper::hasPermission(2, 'can_jump'));
	}

	public function test_user_1_has_can_dance_permission()
	{
		User::find(1)->permissions()->attach(1);

		$this->assertTrue(Keeper::hasPermission(1, 'can_dance'));
	}

	public function test_user_2_has_not_can_dance_permission()
	{
		$this->assertFalse(Keeper::hasPermission(2, 'can_dance'));
	}

	public function test_user_2_has_can_drive_permission()
	{
		User::find(2)->roles()->attach(2);
		Role::find(2)->permissions()->attach(3);

		$this->assertTrue(Keeper::hasPermission(2, 'can_drive'));
	}

	public function test_user_1_has_not_can_drive_permission()
	{
		$this->assertFalse(Keeper::hasPermission(1, 'can_drive'));
	}
}