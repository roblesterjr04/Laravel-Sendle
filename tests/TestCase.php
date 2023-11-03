<?php

namespace Lester\Sendle\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Http;

abstract class TestCase extends BaseTestCase
{
	use CreatesApplication;
	
	public function commonClientAssertions()
	{
		
		Http::assertSent(function (Request $request, Response $response) {
			return $request->hasHeader('Authorization') &&
				   Str::of($request->url())->contains('sendle') &&
				   $request->hasHeader('accept');
		});
		
	}
}
