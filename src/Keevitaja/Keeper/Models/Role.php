<?php namespace Keevitaja\Keeper\Models;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Eloquent;

class Role extends Eloquent {

	/**
	 * Guarded attributes
	 *
	 * @var array
	 */
	protected $guarded = ['id', 'created_at', 'updated_at'];

	/**
	 * Permissions table relationsship
	 *
	 * @return object
	 */
	public function permissions()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\Permission');
	}

	/**
	 * Users table pivot relationsship
	 *
	 * @return object
	 */
	public function users()
	{
		return $this->belongsToMany('Keevitaja\Keeper\Models\User');
	}
}