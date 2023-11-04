<?php

namespace Sendle\Models;

class Order extends SendleModel
{
	
	protected $required = [
		'sender' => Entity::class,
		'receiver' => Entity::class,
		'description' => 'string',
		'weight' => 'array',
	];
	
	public function endpoint()
	{
		return 'orders';
	}
	
	public function setSenderAttribute($input)
	{
		$this->attributes['sender'] = (new Entity($input))->validate();
	}
	
	public function setReceiverAttribute($input)
	{
		$this->attributes['receiver'] = (new Entity($input))->validate();
	}
	
}