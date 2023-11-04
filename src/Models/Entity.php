<?php

namespace Sendle\Models;

class Entity extends SendleModel
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
		$this->attributes['contact'] = (new Contact($input))->validate();
	}
	
	public function setAddressAttribute($input)
	{
		$this->attributes['address'] = (new Address($input))->validate();
	}
}