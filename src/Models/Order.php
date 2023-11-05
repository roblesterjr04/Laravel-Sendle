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
		if (is_array($input)) $input = new Entity($input);
		$this->attributes['sender'] = $input->validate();
	}
	
	public function setReceiverAttribute($input)
	{
		if (is_array($input)) $input = new Entity($input);
		$this->attributes['receiver'] = $input->validate();
	}
	
	public function setLabelsAttribute($labels)
	{
		$this->attributes['labels'] = array_map(function($label) {
			return (object)$label;
		}, $labels);
	}
	
}