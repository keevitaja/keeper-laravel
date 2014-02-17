<?php namespace Keevitaja\Keeper\Models;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Eloquent;

class User extends Eloquent {

	/**
	 * Guarded attributes
	 *
	 * @var array
	 */
	protected $guarded = ['id', 'created_at', 'updated_at'];

	/**
	 * Hidden attributes
	 *
	 * @var array
	 */
	protected $hidden = ['password'];

	/**
	 * Roles table relationsship
	 *
	 * @return object
	 */
	public function roles()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\Role');
	}

	/**
	 * Permissions table relationship
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
	 * Determine if user belongs to role
	 *
	 * @param  integer $userId
	 * @param  string $roleName
	 *
	 * @return boolean
	 */
	public function hasRole($userId, $roleName)
	{
		return $this->find($userId)->role($roleName)->exists();
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
		return $this->find($userId)->permission($permissionName)->exists();
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
}