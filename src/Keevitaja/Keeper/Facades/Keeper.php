<?php namespace Keevitaja\Keeper\Facades;

use Illuminate\Support\Facades\Facade;

class Keeper extends Facade {

	protected static function getFacadeAccessor() { return 'keeper'; }
}