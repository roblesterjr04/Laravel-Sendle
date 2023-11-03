<?php

namespace Lester\Sendle\Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = new \Illuminate\Foundation\Application(
			realpath(__DIR__.'/../')
		);

		$app->singleton(
			Kernel::class,
			\Lester\Sendle\Tests\Kernel::class
		);

		$app->make(Kernel::class)->bootstrap();

		return $app;
	}
}
