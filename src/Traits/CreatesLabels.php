<?php

namespace Sendle\Traits;

use Sendle\Models\Order;
use Sendle\Models\Entity;
use Storage;

trait CreatesLabels
{
	
	public function sendleOrderCreate($description, $weight, Entity $receiver = null, Entity $sender = null)
	{
		$order = new Order([
			'idempotency_key' => $this->orderHash(),
			'sender' => $sender?->toArray() ?? config('sendle.default_sender'),
			'receiver' => $receiver?->toArray() ?? $this->sendleReceiver(),
			'description' => $description,
			'weight' => [
				'value' => $weight,
				'units' => config('sendle.units', 'oz'),
			]
		]);
		
		$order->create();
		
		if (config('sendle.save_labels')) $this->sendleSaveLabel($order);
		
		return $order;
	}
	
	public function sendleOrderDelete($orderId = null)
	{
		return Order::find($orderId ?? $this->sendle_order_id)->delete();
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
	
	public function labelUrl()
	{
		
	}
	
	public function labelDownload()
	{
		
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
		foreach ($order->labels as $label) {
			$stream = fopen($label->url, 'r');
			$filename = "{$order->order_id}_{$label->size}.{$label->format}";
			Storage::disk(config('sendle.label_disk'))->put($filename, $stream);
			$label->path = Storage::path($filename);
		}
	}
	
	private function orderHash()
	{
		return md5(implode('.', $this->orderHashFields()));
	}
	
}