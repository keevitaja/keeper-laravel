<?php namespace Keevitaja\Keeper;

use Illuminate\Support\Facades\Facade;

class KeeperFacade extends Facade {

	protected static function getFacadeAccessor() { return 'keeper'; }
}