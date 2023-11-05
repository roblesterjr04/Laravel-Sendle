<?php

return [
	
	/***
		Sendle API Configuration
	 */
	 
	'sendle_id' => env('SENDLE_ID', ''),
	'api_key' => env('SENDLE_API_KEY', ''),
	'sandbox' => env('SENDLE_SANDBOX', false),
	
	'units' => 'oz',
	
	'save_labels' => true,
	
	'label_disk' => config('filesystems.default'),
	
	'default_sender_entity' => [
		'contact' => [
			'name' => env('APP_NAME', 'Laravel'),
			'email' => env('MAIL_FROM_ADDRESS', 'example@test.com'),
		],
		
		// YOU REALLY SHOULD CHANGE THIS.
		
		'address' => [
			'address_line1' => fake()->streetAddress(),
			'address_line2' => fake()->secondaryAddress(),
			'suburb' => fake()->city(),
			'postcode' => fake()->postcode(),
			'state_name' => fake()->stateAbbr(),
			'country' => 'US'
		],
	]
	
];