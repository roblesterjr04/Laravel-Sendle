<?php

namespace Lester\Sendle;

use Http;
use Lester\Sendle\Contracts\SendleContract;
use Lester\Sendle\Exceptions\InvalidRequest;
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
	
	public function get(SendleContract $model)
	{
		$response = $this->send($model->endpoint());
		
		if ($response->ok()) {
			return collect($response->json())->map(function($object) use ($model) {
				return (clone $model)->fill($object);
			});
		}
		
		$this->handleExceptions($response);
	}
	
	public function create(SendleContract $model, $attributes = [])
	{
		$response = $this->send($model->endpoint(), 'post', $attributes ?? $model->toArray());
		
		if ($response->created()) {
			return $model->fill($response->json());
		}
		
		$this->handleExceptions($response);
	}
	
	public function find(SendleContract $model, $id = null)
	{
		$id = $id ?? $model->id;
		$response = $this->send($model->endpoint() . "/$id");
		
		if ($response->ok()) {
			return $model->fill($response->json());
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