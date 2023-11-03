<?php

namespace Lester\Sendle\Tests;

use Lester\Sendle\Facades\Sendle;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Str;
use Http;

class SendleRequestTest extends TestCase
{
	
	public function test_requests_are_formed()
	{
		Http::fake();
			
		$response = Sendle::getRequest()->get('/testing');
			
		$this->commonClientAssertions();
	}
	
	public function test_requests_can_be_sent()
	{
		Http::fake();
			
		$response = Sendle::send('/testing');
			
		$this->commonClientAssertions();
	}
	
}