<?php

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

class KeeperTest extends TestCase {

	protected $cache;
	protected $keeper;

	public function tearDown()
	{
		Mockery::close();
	}

	public function setUp()
	{
		parent::setUp();

		$this->cache = Mockery::mock('Keevitaja\Keeper\KeeperCache');
		
		App::instance('Keevitaja\Keeper\KeeperCache', $this->cache);

		$this->keeper = App::make('Keevitaja\Keeper\Keeper');
	}

	public function test_user_has_role()
	{
		$this->cache->shouldReceive('get')->once()->andReturn(true);

		$result = $this->keeper->hasRole(1, 'admin');

		$this->assertTrue($result);
	}

	public function test_user_has_permission()
	{
		$this->cache->shouldReceive('get')->once()->andReturn(true);

		$result = $this->keeper->hasPermission(1, 'can_dance');

		$this->assertTrue($result);
	}
}