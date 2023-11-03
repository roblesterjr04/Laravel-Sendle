<?php

namespace Lester\Sendle\Tests;

use Lester\Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Http;
use Exception;
use Lester\Sendle\Models\Order;
use Lester\Sendle\Exceptions\InvalidRequest;
use Lester\Sendle\Exceptions\RepeatRequest;

class SendleOrderTest extends TestCase
{
	
	public function test_orders_can_be_instantiated()
	{
		$order = new Order();
		
		$this->assertNotNull($order);
	}
	
	public function test_orders_can_be_found()
	{
		$this->expectException(Exception::class);
			
		Http::fake([
			'api/orders/order_12345' => Http::sequence()
				->push([
					'sendle_reference' => 'ref_number',
				], 200)
				->push([
					'error_description' => 'Not Found'
				], 404)
				->push([
					'error_description' => 'Server Error'
				], 500)
		]);
			
		$orderFound = Order::find('order_12345');
		$orderNope = Order::find('order_12345');
				
		$this->commonClientAssertions();
		
		Http::assertSent(function(Request $request, Response $response) {
			return Str::of($request->url())->contains('order_12345') && $response->ok();
		});
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->notFound();
		});
	}
	
	public function test_orders_can_be_created()
	{
			
		Http::fake([
			'api/orders' => Http::sequence()
				->push([
					'sendle_reference' => 'ref_number',
				], 201)
				->push([
					'error_description' => 'Invalid request data'
				], 422)
				->push([
					'error_description' => 'Server Error'
				], 500)
				->push([
					'error_description' => 'Repeat Request'
				], 425),
		]);
			
		$orderSuccess = Order::create([
			'details' => 'details'
		]);
		
		$this->expectException(InvalidRequest::class);
		$orderFailure = Order::create([
			'details' => 'details'
		]);
		
		$orderServerFail = Order::create([
			'details' => 'details'
		]);
		
		$this->expectException(RepeatRequest::class);
		$orderRepeat = Order::create([
			'details' => 'details'
		]);
		
		$this->commonClientAssertions();
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->created();
		});
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->unprocessableEntity();
		});
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->serverError();
		});
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->status() == 425;
		});
		
	}
	
	public function test_order_can_be_deleted()
	{
		$this->expectException(Exception::class);
			
		Http::fake([
			'api/orders/order_12345' => Http::sequence()
				->push([
					'sendle_reference' => 'ref_number',
				], 200)
				->push([
					'error_description' => 'Not Found'
				], 404)
		]);
			
		$orderFound = Order::delete('order_12345');
		$orderNope = (new Order(['id' => 'order_12345']))->delete();
				
		$this->commonClientAssertions();
		
		Http::assertSent(function(Request $request, Response $response) {
			return Str::of($request->url())->contains('order_12345') && $response->ok();
		});
		
		Http::assertSent(function(Request $request, Response $response) {
			return Str::of($request->url())->contains('order_12345') && $response->notFound();
		});
		
		$this->assertInstanceOf(Order::class, $orderFound);
	}
	
}