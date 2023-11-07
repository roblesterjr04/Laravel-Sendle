# Laravel Sendle

[![Actions Status](https://github.com/roblesterjr04/Laravel-Sendle/workflows/Unit%20Testing/badge.svg)](https://github.com/roblesterjr04/Laravel-Sendle/actions)
[![Latest Stable Version](https://img.shields.io/packagist/v/rob-lester-jr04/laravel-sendle.svg)](https://packagist.org/packages/rob-lester-jr04/laravel-sendle)
[![Packagist](https://img.shields.io/packagist/dm/rob-lester-jr04/laravel-sendle.svg)](https://packagist.org/packages/rob-lester-jr04/laravel-sendle)
[![Packagist](https://img.shields.io/packagist/l/rob-lester-jr04/laravel-sendle.svg)](https://packagist.org/packages/rob-lester-jr04/laravel-sendle)

A simple API wrapper for the Sendle shipping APIs

## Installation

Install via composer

```bash
composer require rob-lester-jr04/laravel-sendle
```

## Basic Usage

This package has a Facade for being used Standalone, in addition to Models that can be instantiated.

### Create an order in Sendle

```php
<?php

use Sendle\Models\Order;

$order = new Order([
	'receiver' => [
		'contact' => [
			'name' => 'John Smith',
			'email' => 'john@example.com',
		],
		'address' => [
			'address_line1' => '123 Main Street',
			'suburb' => 'New York',
			'postcode' => '10010',
			'state_name' => 'NY',
			'country' => 'US'
		],
		'instructions' => 'Leave it on the step',
	],
	'description' => 'test package',
	'product_code' => 'STANDARD-PICKUP',
	'weight' => [
		'value' => 14,
		'units' => 'oz',
	]
]);

$order->create();

// OR....

Order::create([
	...
]);

```

### Retrieve Order

```php
<?php

use Sendle\Models\Order;

Order::find('####');

```

### Get Product Catelog

```php
<?php

use Sendle\Models\Product;
use Sendle\Models\Entity;

$receiver = new Entity([
	'contact' => [
		'name' => 'John Smith',
		'email' => 'john@example.com',
	],
	'address' => [
		'address_line1' => '123 Main Street',
		'suburb' => 'New York',
		'postcode' => '10010',
		'state_name' => 'NY',
		'country' => 'US'
	],
	'instructions' => 'Leave it on the step',
]);

$weight = 26.0;

$products = Product::get($weight, $receiver);

```

## Packages Trait

You can include the `SendsPackages` trait on your models, such as a `Client` model or `User` model

```php
<?php

namespace App\Models;

use Sendle\Traits\SendsPackages;

class User extends Model
{
	use SendsPackages;
	
	protected $sendleOrderCreate = [
		// Map your users address fields to sendle. sendle=>model
		'address_line1' => 'street_address',
		'suburb' => 'city',
		'state_name' => 'state',
	];
	
	//...
}

//.........//

$user = User::find(4);

$user->sendleOrderCreate('grandmas china', 36.0);

```
	

## License

Laravel Sendle is open-sourced software licensed under the [MIT license](LICENSE.md).
