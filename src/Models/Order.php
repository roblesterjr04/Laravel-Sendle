<?php

namespace Sendle\Models;

use Http;
use Storage;
use Sendle\Contracts\SendleContract;

class Order extends SendleModel implements SendleContract
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
																					
			Storage::disk(config('sendle.label_disk'))->put($this->labelFileName($label->size), $stream);
			
			$label->path = Storage::path($this->labelFileName($label->size));
		}
	}
	
	public function getLabel($size = 'cropped')
	{
		if (config('sendle.save_labels')) 
			return Storage::disk(config('sendle.label_disk'))->path($this->labelFileName($size));
		
		foreach ($this->labels as $label) {
			if ($label->size == $size) return $label->url;
		}
		
		return null;
	}
	
	public function labelFileName($size = 'cropped')
	{
		return "{$this->order_id}_{$size}.pdf";
	}
	
	public function labelUrl($size = 'cropped', $method = 'url')
	{
		if (config('sendle.save_labels')) 
			return Storage::disk(config('sendle.label_disk'))->$method($this->labelFileName($size));
		
		foreach ($this->labels as $label) {
			if ($label->size == $size) return $label->url;
		}
		
		return null;
	}
	
	public function downloadLabel($size = 'cropped')
	{
		if (config('sendle.save_labels')) return Storage::disk(config('sendle.label_disk'))->download($this->getLabel($size));
	}
	
}