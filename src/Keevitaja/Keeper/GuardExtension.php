<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Illuminate\Auth\Guard;
use Keeper as K;

class GuardExtension extends Guard {

	/**
	 * Determine if logged user belongs to role
	 *
	 * @param  integer $userId
	 * @param  string $roleName
	 *
	 * @return boolean
	 */
	public function hasRole($roleName)
	{
		if ( ! $this->check()) return false;

		return K::hasRole($this->user()->getAuthIdentifier(), $roleName);
	}

	/**
	 * Determine if logged user has permission
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	public function hasPermission($permissionName)
	{
		if ( ! $this->check()) return false;

		return K::hasPermission($this->user()->getAuthIdentifier(), $permissionName);
	}
}