<?php

namespace Lester\Sendle\Models;

class Order extends SendleModel
{
	
	protected $required = [
		
	];
	
	public function endpoint()
	{
		return 'orders';
	}
	
}