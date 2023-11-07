<?php

namespace Sendle\Tests;

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
	
	public static function product()
	{
		return [
		  'quote' => [
			'gross' => [
			  'amount' => 4.62,
			  'currency' => 'USD',
			],
			'net' => [
			  'amount' => 4.62,
			  'currency' => 'USD',
			],
			'tax' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
		  ],
		  'plan' => 'Sendle Premium',
		  'eta' => [
			'days_range' => [
			  0 => 2,
			  1 => 3,
			],
			'date_range' => [
			  0 => '2023-11-08',
			  1 => '2023-11-09',
			],
			'for_send_date' => '2023-11-06',
		  ],
		  'route' => [
			'type' => 'national',
			'description' => 'United States of America to United States of America',
		  ],
		  'allowed_packaging' => 'any',
		  'product' => [
			'code' => 'SAVER-PICKUP',
			'name' => 'Saver Pickup',
			'first_mile_option' => 'pickup',
			'service' => 'saver',
		  ],
		  'price_breakdown' => [
			'base' => [
			  'amount' => 4.62,
			  'currency' => 'USD',
			],
			'base_tax' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'cover' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'cover_tax' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'discount' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'discount_tax' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'fuel_surcharge' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
			'fuel_surcharge_tax' => [
			  'amount' => 0,
			  'currency' => 'USD',
			],
		  ],
		  'tax_breakdown' => [
		  ],
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
	
	public static function entity()
	{
		return [
			'contact' => self::contact(),
			'address' => self::address(),
		];
	}
	
	public static function newOrderPayload()
	{
		return [
			'receiver' => [
				'contact' => self::contact(),
				'address' => self::address(),
				'instructions' => '',
			],
			'description' => 'test package',
			'product_code' => 'STANDARD-PICKUP',
			'weight' => [
				'value' => fake()->randomNumber(2),
				'units' => 'oz',
			]
		];
	}
}