<?php

namespace Lester\Sendle\Tests;

class FakeHttpPayloads
{	
	public static function newOrderResponse()
	{
		return [
			'sender' => [
				
			],
			'receiver' => [
				
			],
			'description' => 'test package',
			'weight' => [
				'value' => '16',
				'units' => 'oz',
			]
			
		]
	}
}