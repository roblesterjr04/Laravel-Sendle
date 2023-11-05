<?php

namespace Sendle\Traits;

use Sendle\Models\Order;
use Sendle\Models\Entity;
use Storage;
use Http;

trait SendsPackages
{
	
	public function sendleOrderCreate($description, $weight, Entity $receiver = null, Entity $sender = null)
	{
		$order = new Order([
			'idempotency_key' => $this->orderHash(),
			'sender' => $sender ?? config('sendle.default_sender_entity'),
			'receiver' => $receiver ?? $this->sendleReceiver(),
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
	
	public function labelUrl($orderId = null, $size = 'cropped', $method = 'url')
	{
		$order = $this->sendleOrderFind($orderId);
		
		if ($order === null) return null;
		
		$filename = "{$order->order_id}_{$size}.pdf";
		
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
		foreach ($order->labels as $label) {
			$stream = Http::withOptions([
					'stream' => true
			])->get($label->url)->toPsrResponse()->getBody();
			
			$filename = "{$order->order_id}_{$label->size}.{$label->format}";
			Storage::disk(config('sendle.label_disk'))->put($filename, $stream);
			$label->path = Storage::path($filename);
		}
	}
	
	public function orderHash()
	{
		$hashFieldValues = [];
		foreach ($this->orderHashFields() as $key) {
			$hashFieldValues[$key] = $this->$key;
		}
		return md5(implode('.', $hashFieldValues));
	}
	
}