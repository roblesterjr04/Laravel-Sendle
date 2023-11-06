<?php

namespace Sendle\Models;

use Sendle\Facades\Sendle;

class Product extends SendleModel
{
	
	public function endpoint()
	{
		return 'products';
	}
	
	public static function get(float $weight, Entity $receiver, Entity $sender = null)
	{
		$sender = $sender ?? new Entity(config('sendle.default_sender_entity'));
		
		return Sendle::get(new static, [
			'sender_suburb' => $sender->suburb,
			'sender_postcode' => $sender->postcode,
			'sender_country' => $sender->country,
			'receiver_suburb' => $receiver->suburb,
			'receiver_postcode' => $receiver->postcode,
			'receiver_country' => $receiver->country,
			'weight_value' => $weight,
			'weight_units' => config('sendle.units', 'oz'),
		]);
	}
	
}