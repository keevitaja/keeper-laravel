<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Cache;

class Keeper {

	/**
	 * Cache
	 *
	 * @var object
	 */
	protected $cache;

	/**
	 * Constructor
	 *
	 * @param object $cache
	 */
	public function __construct(Cache $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Determine if user belongs to role
	 *
	 * @param  integer $userId
	 * @param  string $roleName
	 *
	 * @return boolean
	 */
	public function hasRole($userId, $roleName)
	{	
		return $this->cache->get($userId, $roleName, 'role');
	}

	/**
	 * Determine if user has a permission
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	public function hasPermission($userId, $permissionName)
	{
		return $this->cache->get($userId, $permissionName, 'permission');
	}

	/**
	 * Flush cache. Works only if cache tags are present.
	 *
	 * @return void
	 */
	public function flushCache()
	{
		$this->cache->flush();
	}
}