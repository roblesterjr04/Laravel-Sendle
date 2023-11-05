<?php

namespace Sendle\Tests;

use Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Http;
use Storage;
use Exception;
use Sendle\Models\Order;
use Sendle\Models\Entity;
use Sendle\Exceptions\InvalidRequest;
use Sendle\Exceptions\RepeatRequest;

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
			
		$orderSuccess = Order::create(FakeHttpPayloads::newOrderPayload());	
		
		$this->expectException(InvalidRequest::class);
		$orderFailure = Order::create(FakeHttpPayloads::newOrderPayload());
		
		$orderServerFail = Order::create(FakeHttpPayloads::newOrderPayload());
		
		$this->expectException(RepeatRequest::class);
		$orderRepeat = Order::create(FakeHttpPayloads::newOrderPayload());
		
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
	
	public function test_order_created_from_model()
	{
		Storage::fake('local');
		Http::fake([
			'api/orders' => Http::response([
				'sendle_reference' => 'ref_number',
				'labels' => [
					[
						'format' => 'pdf',
						'size' => 'a4',
						'url' => 'https://api.sendle.com/api/orders/f5233746-71d4-4b05-bf63-56f4abaed5f6/labels/a4.pdf',
					],
					[
						'format' => 'pdf',
						'size' => 'cropped',
						'url' => 'https://api.sendle.com/api/orders/f5233746-71d4-4b05-bf63-56f4abaed5f6/labels/cropped.pdf',
					]
				]
			], 201),
			'api/orders/*' => Http::response(fopen('php://temp','r'), 200),
		]);
			
		$id = fake()->randomNumber();
		$ts = now();
		
		$model = new TestModel([
			'updated_at' => $ts,
			'id' => $id,
		]);
		
		$receiverEntity = new Entity(FakeHttpPayloads::entity());
					
		$model->sendleOrderCreate("Test order create", 12, $receiverEntity);
	}
	
}