<?php

namespace Sendle\Models;

class Contact extends SendleModel
{
	protected $required = [
		'name' => 'string',
	];
	
	public function endpoint()
	{
		return null;
	}
}