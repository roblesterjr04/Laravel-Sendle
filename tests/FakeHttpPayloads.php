<?php

namespace Sendle\Tests;

use Faker\Factory;

class FakeHttpPayloads
{	
	public static function contact()
	{
		return [
			'name' => fake()->name(),
			'email' => fake()->safeEmail(),
			'phone' => fake()->phoneNumber(),
			'company' => fake()->company(),
		];
	}
	
	public static function address()
	{
		return [
			'address_line1' => fake()->streetAddress(),
			'address_line2' => fake()->secondaryAddress(),
			'suburb' => fake()->city(),
			'postcode' => fake()->postcode(),
			'state_name' => fake()->stateAbbr(),
			'country' => 'US'
		];
	}
	
	public static function newOrderPayload()
	{
		return [
			'sender' => [
				'contact' => self::contact(),
				'address' => self::address(),
			],
			'receiver' => [
				'contact' => self::contact(),
				'address' => self::address(),
				'instructions' => '',
			],
			'description' => 'test package',
			'weight' => [
				'value' => fake()->randomNumber(2),
				'units' => 'oz',
			]
		];
	}
}

if (!function_exists('fake')) {
	function fake() {
		return Factory::create();
	}
}