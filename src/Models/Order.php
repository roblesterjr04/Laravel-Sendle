<?php

namespace Lester\Sendle\Models;

class Order extends SendleModel
{
	
	protected $required = [
		'sender' => Entity::class,
		'receiver' => Entity::class,
		'description' => 'string',
		'weight' => 'string',
	];
	
	public function endpoint()
	{
		return 'orders';
	}
	
}