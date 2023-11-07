<?php

namespace Sendle\Tests;

use Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Str;
use Http;
use Sendle\Models\Entity;
use Sendle\Tests\TestModel;

class SendleBaseTest extends TestCase
{
	
	public function test_service_provider_can_register()
	{
		$this->assertNotNull(Sendle::instance());
	}
	
	public function test_http_tests_work()
	{
		Http::fake();
		
		$response = Http::get('/testing/endpoint');
		
		Http::assertSent(function (Request $request) {
			return Str::of($request->url())->contains('test');
		});
	}
	
	public function test_model_attributes()
	{
		$model = new Entity();
		
		$this->assertEquals('N/A', $model->instructions);
	}
	
	public function test_eloquent_model()
	{
		$id = fake()->randomNumber();
		$ts = now();
		
		$model = new TestModel([
			'updated_at' => $ts,
			'id' => $id,
		]);
		
		$hash = md5("$id.$ts");
				
		$this->assertInstanceOf(TestModel::class, $model);
		$this->assertEquals($hash, $model->orderHash());
	}
		
}