<?php namespace Keevitaja\Keeper;

/**
* @author Tanel Tammik <keevitaja@gmail.com>
* @copyright Copyright (c) 2014
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Keevitaja\Keeper\GuardExtension;
use Auth;
use Config;

class KeeperServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap application
	 *
	 * @return void
	 */
	public function boot()
	{
		Auth::extend('keeper', function() 
		{
			$provider = new EloquentUserProvider(
				$this->app->make('hash'), 
				Config::get('auth.model')
			);

			$session = $this->app->make('session.store');

			return new GuardExtension($provider, $session);
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('keeper', 'Keevitaja\Keeper\Keeper');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}