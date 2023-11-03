<?php

namespace Lester\Sendle\Models;

class Entity extends SendleModel
{
	protected $required = [
		'contact' => Contact::class,
		'address' => Address::class,
	];
}