<?php

namespace Sendle\Tests;

use Sendle\Traits\SendsPackages;
use Sendle\Models\SendleModel;

class TestModel extends SendleModel
{
	use SendsPackages;
	
	protected $sendleAddressMap = [
		'address_line1' => 'street_address',
		'suburb' => 'city',
		'state_name' => 'state',
	];
	
}