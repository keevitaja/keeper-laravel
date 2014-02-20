<?php namespace Keevitaja\Keeper\Models\Traits;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\Exceptions\PermissionNotFoundException;

trait PermissionTrait {

	/**
	 * Users table pivot relationship
	 *
	 * @return object
	 */
	public function users()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\User');
	}

	/**
	 * Roles table pivot relationship
	 *
	 * @return object
	 */
	public function roles()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\Role');
	}
}