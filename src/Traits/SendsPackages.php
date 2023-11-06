<?php

namespace Sendle\Traits;

use Sendle\Models\Order;
use Sendle\Models\Product;
use Sendle\Models\Entity;
use Storage;
use Http;

trait SendsPackages
{
	protected $sendleAddressMap = [
		'suburb' => 'city',
		'state_name' => 'state',
	];
	
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
		
		if (config('sendle.save_labels')) $this->sendleSaveLabel($order);
		
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
				'suburb' => $this->city,
				'postcode' => $this->postcode,
				'state_name' => $this->state,
				'country' => $this->country ?? 'US'
			]
		]))->validate();
	}
	
	public function labelPath($orderId = null, $size = 'cropped')
	{
		$order = $this->sendleOrderFind($orderId);
		
		if ($order === null) return null;
		
		$filename = "{$order->order_id}_{$size}.pdf";
		
		if (config('sendle.save_labels')) return Storage::disk(config('sendle.label_disk'))->path($filename);
		
		foreach ($order->labels as $label) {
			if ($label->size == $size) return $label->url;
		}
		
		return null;
	}
	
	public function labelFileName($orderId = null, $size = 'cropped')
	{
		$order = $this->sendleOrderFind($orderId);
		
		if ($order === null) return null;
		
		$filename = "{$order->order_id}_{$size}.pdf";
		
		return $filename;
	}
	
	public function labelUrl($orderId = null, $size = 'cropped', $method = 'url')
	{
		$order = $this->sendleOrderFind($orderId);
		
		if ($order === null) return null;
		
		$filename = $this->labelFileName($orderId, $size);
		
		if (config('sendle.save_labels')) return Storage::disk(config('sendle.label_disk'))->$method($filename);
		
		foreach ($order->labels as $label) {
			if ($label->size == $size) return $label->url;
		}
		
		return null;
	}
	
	public function labelDownload($orderId = null, $size = 'cropped')
	{
		
		$labelPath = $this->labelPath($orderId, $size);
		
		if ($labelPath === null) return null;
		
		if (config('sendle.save_labels')) return Storage::disk(config('sendle.label_disk'))->download($labelPath);
		
	}
	
	public function orderHashFields()
	{
		return [
			'id',
			'updated_at',
		];
	}
	
	private function sendleSaveLabel(Order $order)
	{
		$order->saveLabel();
	}
	
	public function orderHash()
	{
		$hashFieldValues = [];
		foreach ($this->orderHashFields() as $key) {
			$hashFieldValues[$key] = $this->$key;
		}
		return md5(implode('.', $hashFieldValues));
	}
	
	public function getAddressLine1Attribute()
	{
		
	}
	
}