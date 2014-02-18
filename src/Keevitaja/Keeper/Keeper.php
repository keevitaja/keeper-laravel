<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\User;
use Keevitaja\Keeper\Models\Role;
use Keevitaja\Keeper\Models\Permission;

class Keeper {

	/**
	 * User model
	 *
	 * @var object
	 */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param object $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
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
		return $this->user->hasRole($userId, $roleName);
	}

	/**
	 * Determine if user has a direct permission
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	protected function hasDirectPermission($userId, $permissionName)
	{
		return $this->user->hasDirectPermission($userId, $permissionName);
	}

	/**
	 * Determine if user has permission through a role
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	protected function hasRolePermission($userId, $permissionName)
	{
		return $this->user->hasRolePermission($userId, $permissionName);
	}

	/**
	 * Determine if user has permission
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	public function hasPermission($userId, $permissionName)
	{
		if ($this->hasDirectPermission($userId, $permissionName)) return true;

		return $this->hasRolePermission($userId, $permissionName);
	}
}