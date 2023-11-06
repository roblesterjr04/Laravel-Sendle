<?php

namespace Sendle\Tests;

use Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Http;
use Storage;
use Exception;
use Sendle\Models\Product;
use Sendle\Models\Entity;
use Sendle\Exceptions\InvalidRequest;
use Sendle\Exceptions\RepeatRequest;

class SendleProductTest extends TestCase
{
	public function test_product_can_be_instantiated()
	{
		$product = new Product();
		
		$this->assertNotNull($product);
	}
	
	public function test_product_catalog_is_array()
	{
		Http::fake([
			'api/products?*' => Http::response([
				FakeHttpPayloads::product()
			], 200),
		]);
		
		$receiver = new Entity(FakeHttpPayloads::entity());
		
		$products = Product::get(12, $receiver);
		
		$this->commonClientAssertions();
		
		Http::assertSent(function(Request $request, Response $response) {
			return $response->ok() && count($response->json());
		});
	}
}