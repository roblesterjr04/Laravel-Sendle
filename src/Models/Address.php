<?php

namespace Sendle\Models;

use Sendle\Contracts\SendleContract;

class Address extends SendleModel implements SendleContract
{
	
	protected $required = [
		'address_line1' => 'string',
		'suburb' => 'string',
		'state_name' => 'string',
		'postcode' => 'string',
	];
	
	public function endpoint()
	{
		return null;
	}
}