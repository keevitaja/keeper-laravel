<?php namespace Keevitaja\Keeper\Models\Traits;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Keevitaja\Keeper\Models\Exceptions\RoleNotFoundException;

trait RoleTrait {

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
	 * Users table pivot relationship
	 *
	 * @return object
	 */
	public function users()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\User');
	}

	/**
	 * Find role scope or throw exception on fail
	 *
	 * @param  integer $roleId
	 *
	 * @return mixed
	 */
	public function scopeFindRole($query, $roleId)
	{
		$role = $query->find($roleId);

		if ( ! is_null($role)) return $role;

		throw new RoleNotFoundException('Role with ID of "' . $roleId . '" was not found!');
	}
}