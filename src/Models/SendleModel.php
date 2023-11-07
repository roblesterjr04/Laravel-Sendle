<?php

namespace Sendle\Models;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Sendle\Facades\Sendle;
use Sendle\Contracts\SendleContract;
use Sendle\Exceptions\MissingRequiredFields;

abstract class SendleModel
{
	
	use HasAttributes;
	
	protected $required = [];
	protected $exists;
	
	public function __construct($data = [])
	{
		$this->fill($data);
	}
	
	public function __debugInfo()
	{
		return $this->toArray();
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
	
	public function __set($key, $value)
	{
		$this->setAttribute($key, $value);
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
			if (!($this->$key instanceof $type || gettype($this->$key) == $type)) {
				$missing[] = $key;
			}
		}
		
		if (count($missing)) {
			throw new MissingRequiredFields(implode(', ', $missing) . ' are required');
		}
		
		return $this;
	}
	
}