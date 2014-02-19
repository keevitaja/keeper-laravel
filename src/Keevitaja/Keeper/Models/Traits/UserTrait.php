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
	 * Role relationship scope
	 *
	 * @param  object $query
	 * @param  string $roleName
	 *
	 * @return object
	 */
	public function scopeRole($query, $roleName)
	{
		return $query->whereHas('roles', function($query) use($roleName)
		{
			$query->whereName($roleName);
		});
	}

	/**
	 * Permission relationship scope
	 *
	 * @param  object $query
	 * @param  string $permissionName
	 *
	 * @return object
	 */
	public function scopePermission($query, $permissionName)
	{
		return $query->whereHas('permissions', function($query) use($permissionName)
		{
			$query->whereName($permissionName);
		});
	}

	/**
	 * Find user scope or throw exception on fail
	 *
	 * @param  integer $userId
	 *
	 * @return mixed
	 */
	public function scopeFindUser($query, $userId)
	{
		$user = $query->find($userId);

		if ( ! is_null($user)) return $user;

		throw new UserNotFoundException('User with ID of "' . $userId . '" was not found!');
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
		return $this->findUser($userId)->role($roleName)->exists();
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
		return $this->findUser($userId)->permission($permissionName)->exists();
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
		return $this->findUser($userId)
			->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
			->leftJoin('permission_role', 'role_user.role_id', '=', 'permission_role.role_id')
			->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
			->where('permissions.name', $permissionName)
			->exists();
	}
}