<?php

namespace Lester\Sendle\Tests;

use Lester\Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Str;
use Http;

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
	
}