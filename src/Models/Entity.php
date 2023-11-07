<?php

namespace Sendle\Models;

use Sendle\Contracts\SendleContract;

class Entity extends SendleModel implements SendleContract
{
	protected $required = [
		'contact' => Contact::class,
		'address' => Address::class,
	];
	
	public function getInstructionsAttribute()
	{
		return $this->attributes['instructions'] ?? 'N/A';
	}
	
	public function endpoint()
	{
		return null;
	}
	
	public function setContactAttribute($input)
	{
		if (is_array($input)) $input = new Contact($input);
		$this->attributes['contact'] = $input->validate();
	}
	
	public function setAddressAttribute($input)
	{
		if (is_array($input)) $input = new Address($input);
		$this->attributes['address'] = $input->validate();
	}
}