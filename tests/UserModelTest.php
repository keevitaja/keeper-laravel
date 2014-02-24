<?php

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\User;
use Keevitaja\Keeper\Models\Role;
use Keevitaja\Keeper\Models\Permission;

class UserModelTest extends TestCase {

	protected $user;
	protected $role;

	public function setUp()
	{
		parent::setUp();

		Artisan::call('migrate', ['--bench' => 'keevitaja/keeper']);

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

		$this->user = App::make('Keevitaja\Keeper\Models\User');
		$this->role = App::make('Keevitaja\Keeper\Models\Role');
	}

	public function test_user_has_role()
	{
		$this->assertFalse($this->user->hasRole(1, 'admin'));

		$this->user->find(1)->roles()->attach(1);

		$this->assertTrue($this->user->hasRole(1, 'admin'));
		$this->assertFalse($this->user->hasRole(1, 'moderator'));
		$this->assertFalse($this->user->hasRole(2, 'admin'));
	}

	public function test_user_has_direct_permission()
	{
		$this->assertFalse($this->user->hasDirectPermission(1, 'can_dance'));

		$this->user->find(1)->permissions()->attach(1);

		$this->assertTrue($this->user->hasDirectPermission(1, 'can_dance'));
		$this->assertFalse($this->user->hasDirectPermission(1, 'can_jump'));
		$this->assertFalse($this->user->hasDirectPermission(2, 'can_dance'));
	}

	public function test_user_has_role_permission()
	{
		$this->assertFalse($this->user->hasRolePermission(2, 'can_dance'));

		$this->role->find(2)->permissions()->attach(1);

		$this->assertFalse($this->user->hasRolePermission(2, 'can_dance'));

		$this->user->find(2)->roles()->attach(2);

		$this->assertTrue($this->user->hasRolePermission(2, 'can_dance'));
		$this->assertFalse($this->user->hasRolePermission(2, 'can_jump'));
		$this->assertFalse($this->user->hasRolePermission(1, 'can_dance'));
	}

	public function test_user_has_permission()
	{
		$this->assertFalse($this->user->hasRolePermission(2, 'can_dance'));
		$this->assertFalse($this->user->hasRolePermission(1, 'can_jump'));

		$this->role->find(2)->permissions()->attach(1);
		$this->user->find(2)->roles()->attach(2);

		$this->assertTrue($this->user->hasPermission(2, 'can_dance'));
		$this->assertFalse($this->user->hasPermission(2, 'can_jump'));
		$this->assertFalse($this->user->hasPermission(1, 'can_dance'));
	}
}