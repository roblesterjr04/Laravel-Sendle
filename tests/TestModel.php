<?php

namespace Sendle\Tests;

use Sendle\Traits\SendsPackages;

class TestModel
{
	use SendsPackages;
		
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