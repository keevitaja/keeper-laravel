<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\User;
use Config;
use Cache;

class KeeperCache {

	/**
	 * User model object
	 *
	 * @var object
	 */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Cache enabled/disabled
	 *
	 * @return boolean
	 */
	protected function useCache()
	{
		return Config::get('keeper::cache');
	}

	/**
	 * Get unique identifier for cache
	 *
	 * @return string
	 */
	protected function cacheId()
	{
		return Config::get('keeper::cache_id');
	}

	/**
	 * Get cache expiration time
	 *
	 * @return int
	 */
	protected function cacheExpire()
	{
		return Config::get('keeper::cache_expire');
	}

	/**
	 * Use cache tags enabled/disabled
	 *
	 * @return boolean
	 */
	protected function cacheTags()
	{
		return Config::get('keeper::cache_tags');
	}

	/**
	 * Make unique key for cache
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return string
	 */
	protected function makeKey($user, $name, $type)
	{
		return $this->cacheId() . '.' . $user . '.' . $name . '.' . $type;
	}

	/**
	 * Get boolean from user model
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return boolean
	 */
	protected function getResult($user, $name, $type)
	{
		$type = 'has' . ucfirst($type);

		return $this->user->$type($user, $name);
	}

	/**
	 * Get boolean, if cache tags are not enabled
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return boolean
	 */
	protected function simple($user, $name, $type)
	{
		$key = $this->makeKey($user, $name, $type);

		if (Cache::has($key)) return Cache::get($key);

		$result = $this->getResult($user, $name, $type);

		Cache::put($key, $result, $this->cacheExpire());

		return $result;
	}

	/**
	 * Get result from with cache tags enabled.
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return boolean
	 */
	protected function tags($user, $name, $type)
	{
		$key = $this->makeKey($user, $name, $type);
		$cacheId = $this->cacheId();

		if (Cache::tags($cacheId)->has($key)) return Cache::tags($cacheId)->get($key);

		$result = $this->getResult($user, $name, $type);

		Cache::tags($cacheId)->put($key, $result, $this->cacheExpire());

		return $result;
	}

	/**
	 * Determine, if user has a role or permission
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return boolean
	 */
	public function get($user, $name, $type)
	{
		if ( ! $this->useCache()) return $this->getResult($user, $name, $type);

		$cacheMethod = $this->cacheTags() ? 'tags' : 'simple';

		return $this->$cacheMethod($user, $name, $type);
	}

	/**
	 * Flush cache
	 *
	 * @return void
	 */
	public function flush()
	{
		Cache::tags($this->cacheId())->flush();
	}
}