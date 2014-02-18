<?php namespace Keevitaja\Keeper\Models;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Eloquent;

class User extends Eloquent {

	use Traits\UserTrait;

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
}