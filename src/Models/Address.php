<?php

namespace Sendle\Models;

class Address extends SendleModel
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