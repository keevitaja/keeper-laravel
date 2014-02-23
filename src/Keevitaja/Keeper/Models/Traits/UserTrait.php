<?php namespace Keevitaja\Keeper\Models\Traits;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\Exceptions\UserNotFoundException;

trait UserTrait {

	/**
	 * Roles table pivot relationship
	 *
	 * @return object
	 */
	public function roles()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\Role');
	}

	/**
	 * Permissions table pivot relationship
	 *
	 * @return object
	 */
	public function permissions()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\Permission');
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
		return $this
			->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
			->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
			->where('users.id', $userId)
			->where('roles.name', $roleName)
			->exists();
	}

	/**
	 * Determine if user has a direct permission
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	public function hasDirectPermission($userId, $permissionName)
	{
		return $this
			->leftJoin('permission_user', 'users.id', '=', 'permission_user.user_id')
			->leftJoin('permissions', 'permissions.id', '=', 'permission_user.permission_id')
			->where('users.id', $userId)
			->where('permissions.name', $permissionName)
			->exists();
	}

	/**
	 * Determine if user has permission through a role
	 *
	 * @param  integer $userId
	 * @param  string $permissionName
	 *
	 * @return boolean
	 */
	public function hasRolePermission($userId, $permissionName)
	{
		return $this
			->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
			->leftJoin('permission_role', 'role_user.role_id', '=', 'permission_role.role_id')
			->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
			->where('users.id', $userId)
			->where('permissions.name', $permissionName)
			->exists();
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
		if ($this->hasDirectPermission($userId, $permissionName)) return true;

		return $this->hasRolePermission($userId, $permissionName);
	}
}