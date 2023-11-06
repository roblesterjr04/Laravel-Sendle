<?php

namespace Sendle;

use Http;
use Sendle\Contracts\SendleContract;
use Sendle\Exceptions\InvalidRequest;
use Illuminate\Http\Client\Response;
use Exception;

class Sendle
{
	private $request;
	
	private const PROD_URL = 'https://www.sendle.com/api';
	private const SAND_URL = 'https://sandbox.sendle.com/api';
	
	public function __construct()
	{
		$user = config('sendle.sendle_id');
		$pass = config('sendle.api_key');
		$base = config('sendle.sandbox') ? self::SAND_URL : self::PROD_URL;
		$this->request = Http::withBasicAuth($user, $pass)
							 ->baseUrl($base)
							 ->withHeaders(['accept' => 'application/json']);
	}
	
	public function instance()
	{
		return $this;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function send($endpoint, $method = 'get', $body = [], $headers = [])
	{
		return $this->request->withHeaders($headers)->$method($endpoint, $body);
	}
	
	public function get(SendleContract $model, $attributes = null)
	{
		$response = $this->send($model->endpoint(), 'get', $attributes);
		
		if ($response->ok()) {
			return collect($response->json())->map(function($object) use ($model) {
				return (clone $model)->fill($object);
			});
		}
		
		$this->handleExceptions($response);
	}
	
	public function create(SendleContract $model, $attributes = null)
	{
		if ($attributes !== null) {
			$model->fill($attributes);
		}
		$response = $this->send($model->validate()->endpoint(), 'post', $model->toArray(), [
			'Idempotency-Key' => $model->idempotecy_key,
		]);
		
		if ($response->created()) {
			return $model->fill($response->json());
		}
		
		$this->handleExceptions($response);
	}
	
	public function return(Order $order, $id = null, $attributes = [], $headers = [])
	{
		$id = $id ?? $model->id;
		$response = $this->send("orders/$id/return", 'post', $attributes, $headers);
		
		if ($response->created() || $response->ok()) {
			return $model->fill($response->json());
		}
		
		$this->handleExceptions($response);
	}
	
	public function find(SendleContract $model, $id = null)
	{
		$id = $id ?? $model->id;
		$response = $this->send($model->endpoint() . "/$id");
		
		if ($response->ok()) {
			return $model->fill($response->json() ?? []);
		}
		
		$this->handleExceptions($response);
	}
	
	public function delete(SendleContract $model, $id = null)
	{
		$id = $id ?? $model->id;
		$response = $this->send($model->endpoint() . "/$id", 'delete');
		
		if ($response->ok()) {
			return true;
		}
		
		$this->handleExceptions($response);
	}
	
	private function handleExceptions(Response $response)
	{
		$error = $response->json('error_description') ?? 'API Failure';
		if ($response->unprocessableEntity()) {
			throw new InvalidRequest($error);
		}
		if ($response->status() == 425) {
			throw new RepeatRequest($error);
		}
		throw new Exception($error);
	}
	
}