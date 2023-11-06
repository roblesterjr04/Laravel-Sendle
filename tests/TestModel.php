<?php

namespace Sendle\Tests;

use Sendle\Traits\SendsPackages;

class TestModel
{
	use SendsPackages;
	
	protected $sendleAddressMap = [
		'address_line1' => 'street_address',
		'suburb' => 'city',
		'state_name' => 'state',
	];
		
	public function __construct($attributes = [])
	{
		foreach ($attributes as $key => $attr) {
			$this->$key = $attr;
		}
	}
	
	public function __get($key)
	{
		if (property_exists($this, $key)) return $this->$key;
		return null;
	}
}