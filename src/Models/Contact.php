<?php

namespace Sendle\Models;

use Sendle\Contracts\SendleContract;

class Contact extends SendleModel implements SendleContract
{
	protected $required = [
		'name' => 'string',
	];
	
	public function endpoint()
	{
		return null;
	}
}