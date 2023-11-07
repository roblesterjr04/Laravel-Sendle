<?php

namespace Sendle\Traits;

use Sendle\Models\Order;
use Sendle\Models\Product;
use Sendle\Models\Entity;
use Storage;
use Http;

trait SendsPackages
{
	public function sendleProducts(float $weight, Entity $receiver = null, Entity $sender = null)
	{
		return Product::get($weight, $receiver ?? $this->sendleReceiver(), $sender);
	}
	
	public function sendleOrderCreate(string $description, float $weight, Entity $receiver = null, Entity $sender = null, Product $product = null)
	{
		$order = new Order([
			'idempotency_key' => $this->orderHash(),
			'sender' => $sender ?? config('sendle.default_sender_entity'),
			'receiver' => $receiver ?? $this->sendleReceiver(),
			'description' => $description,
			'product_code' => $product?->product?->code,
			'weight' => [
				'value' => $weight,
				'units' => config('sendle.units', 'oz'),
			]
		]);
		
		$order->create();
		
		if (config('sendle.save_labels')) $order->saveLabel();
		
		return $order;
	}
	
	public function sendleOrderFind($orderId = null)
	{
		return Order::find($orderId ?? $this->sendle_order_id);
	}
	
	public function sendleOrderDelete($orderId = null)
	{
		return $this->sendleOrderFind($orderId)->delete();
	}
	
	public function sendleReceiver()
	{
		return (new Entity([
			'contact' => [
				'name' => $this->name,
				'email' => $this->email,
				'phone' => $this->phone,
				'company' => $this->company,
			],
			'address' => [
				'address_line1' => $this->address_line1,
				'address_line2' => $this->address_line2,
				'suburb' => $this->suburb,
				'postcode' => $this->postcode,
				'state_name' => $this->state_name,
				'country' => $this->country ?? 'US'
			]
		]))->validate();
	}
	
	public function orderHashFields()
	{
		return [
			'id',
			'updated_at',
		];
	}
	
	public function orderHash()
	{
		$hashFieldValues = [];
		foreach ($this->orderHashFields() as $key) {
			$hashFieldValues[$key] = $this->$key;
		}
		return md5(implode('.', $hashFieldValues));
	}
	
	protected function sendleAddressFieldMap()
	{
		return $this->sendleAddressMap ?? [
			'suburb' => 'city',
			'state_name' => 'state',
		];
	}
	
	public function getAttribute($key)
	{
		$mapKeys = $this->sendleAddressFieldMap();
		return parent::getAttribute($mapKeys[$key] ?? $key);
	}
	
	public function getSendleOrderAttribute()
	{
		return Order::find($this->order_id);
	}
	
}