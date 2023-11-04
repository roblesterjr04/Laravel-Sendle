<?php

namespace Sendle\Facades;

use Illuminate\Support\Facades\Facade;

class Sendle extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'sendle';
	}
}
