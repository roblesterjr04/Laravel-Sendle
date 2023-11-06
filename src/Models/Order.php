<?php

namespace Sendle\Models;

use Http;
use Storage;

class Order extends SendleModel
{
	
	public const UNITS = [
		'kg', 'lb', 'g', 'oz'
	];
	
	protected $required = [
		'sender' => Entity::class,
		'receiver' => Entity::class,
		'description' => 'string',
		'weight' => 'array',
	];
	
	public function endpoint()
	{
		return 'orders';
	}
	
	public function setSenderAttribute($input)
	{
		if (is_array($input)) $input = new Entity($input);
		$this->attributes['sender'] = $input->validate();
	}
	
	public function setReceiverAttribute($input)
	{
		if (is_array($input)) $input = new Entity($input);
		$this->attributes['receiver'] = $input->validate();
	}
	
	public function setLabelsAttribute($labels)
	{
		$this->attributes['labels'] = array_map(function($label) {
			return (object)$label;
		}, $labels);
	}
	
	public function saveLabel()
	{
		foreach ($this->labels as $label) {
			$stream = Http::withOptions([
					'stream' => true
			])->get($label->url)->body();
																					
			$filename = "{$this->order_id}_{$label->size}.pdf";
			Storage::disk(config('sendle.label_disk'))->put($filename, $stream);
			
			$label->path = Storage::path($filename);
		}
	}
	
}