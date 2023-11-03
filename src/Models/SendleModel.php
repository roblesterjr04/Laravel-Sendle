<?php

namespace Lester\Sendle\Models;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Lester\Sendle\Facades\Sendle;
use Lester\Sendle\Contracts\SendleContract;
use Lester\Sendle\Exceptions\MissingRequiredFields;

abstract class SendleModel implements SendleContract
{
	
	use HasAttributes;
	
	protected $required = [];
	protected $exists;
	
	public function __construct($data = [])
	{
		$this->fill($data);
	}
	
	public function fill(array $attributes)
	{
		foreach ($attributes as $key => $value) {
			$this->setAttribute($key, $value);
		}
	
		return $this;
	}
	
	public function toArray()
	{
		return $this->attributes;
	}
	
	public function __get($key)
	{
		return $this->getAttribute($key);
	}
	
	public function relationResolver()
	{
		return null;
	}
	
	public function relationLoaded()
	{
		return false;
	}
	
	private function usesTimestamps()
	{
		return false;
	}
	
	private function getIncrementing()
	{
		return false;
	}

	public function __call($name, $arguments)
	{
		return Sendle::$name($this, ...$arguments);
	}
	
	public static function __callStatic($name, $arguments)
	{
		$instance = new static();
		
		return $instance->$name(...$arguments);
	}
	
	public function validate()
	{
		$missing = [];
		
		foreach ($this->required as $key => $type) {
			if ($this->$key === null) $missing[] = $key;
		}
		
		if (count($missing)) {
			throw new MissingRequiredFields(implode(', ', $missing) . ' are required');
		}
		
		return $this;
	}
	
}