<?php

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

class KeeperCacheTest extends TestCase {

	protected $user;
	protected $cache;

	public function tearDown()
	{
		Mockery::close();
	}

	public function setUp()
	{
		parent::setUp();

		$this->user = Mockery::mock('Keevitaja\Keeper\Models\User');

		App::instance('Keevitaja\Keeper\Models\User', $this->user);

		$this->cache = App::make('Keevitaja\Keeper\KeeperCache');
	}

	public function test_user_has_role()
	{
		$this->user->shouldReceive('hasRole')->once()->andReturn(true);

		$result = $this->cache->get(1, 'admin', 'role');

		$this->assertTrue($result);
	}

	public function test_user_has_role_with_cache()
	{
		Config::set('keeper::cache', true);

		$this->user->shouldReceive('hasRole')->once()->andReturn(true);

		$result = $this->cache->get(1, 'admin', 'role');

		$this->assertTrue($result);

		$this->user->shouldReceive('hasRole')->times(0)->andReturn(false);

		$result = $this->cache->get(1, 'admin', 'role');

		$this->assertTrue($result);

		Cache::flush();

		$this->user->shouldReceive('hasRole')->once()->andReturn(true);

		$result = $this->cache->get(1, 'admin', 'role');

		$this->assertTrue($result);
	}

	public function test_user_has_permission()
	{
		$this->user->shouldReceive('hasPermission')->once()->andReturn(true);

		$result = $this->cache->get(1, 'can_dance', 'permission');

		$this->assertTrue($result);
	}

	public function test_user_has_permission_with_cache_tags()
	{
		Config::set('keeper::cache', true);
		Config::set('keeper::cache_tags', true);

		$this->user->shouldReceive('hasPermission')->once()->andReturn(true);

		$result = $this->cache->get(1, 'can_dance', 'permission');

		$this->assertTrue($result);

		$this->user->shouldReceive('hasPermission')->times(0)->andReturn(true);

		$result = $this->cache->get(1, 'can_dance', 'permission');

		$this->assertTrue($result);
	}
}