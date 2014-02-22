<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Config;
use Cache as SystemCache;
use Keevitaja\Keeper\Models\User;

class Cache {

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
	 * Make key for cache
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return string
	 */
	protected function makeKey($user, $name, $type)
	{
		return Config::get('keeper::cache_id') . '.' . $user . '.' . $name . '.' . $type;
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

		if (SystemCache::has($key)) return SystemCache::get($key);

		$result = $this->getResult($user, $name, $type);

		SystemCache::put($key, $result, Config::get('keeper::cache_expire'));

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

		$cacheId = Config::get('keeper::cache_id');

		if (SystemCache::tags($cacheId)->has($key)) return SystemCache::tags($cacheId)->get($key);

		$result = $this->getResult($user, $name, $type);

		SystemCache::tags($cacheId)->put($key, $result, Config::get('keeper::cache_expire'));

		return $result;
	}

	/**
	 * Get result from without cache tags enabled.
	 *
	 * @param  integer $user
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return boolean
	 */
	public function get($user, $name, $type)
	{
		if ( ! Config::get('keeper::cache')) return $this->getResult($user, $name, $type);

		$cacheMethod = Config::get('keeper::cache_tags') ? 'tags' : 'simple';

		return $this->$cacheMethod($user, $name, $type);
	}

	/**
	 * Flush cache
	 *
	 * @return void
	 */
	public function flush()
	{
		SystemCache::tags(Config::get('keeper::cache_id'))->flush();
	}
}