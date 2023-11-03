<?php

namespace Lester\Sendle;

use Lester\Sendle\Facades\Sendle as SendleFacade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	const CONFIG_PATH = __DIR__ . '/../config/sendle.php';
	
	public function boot()
	{
		
		$this->publishes([
			self::CONFIG_PATH => config_path('sendle.php'),
		], 'config');
		
	}
	
	public function register()
	{
		$this->mergeConfigFrom(
			self::CONFIG_PATH,
			'sendle'
		);
	
		$this->app->bind('sendle', function() {
			return new Sendle();
		});
	
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Sendle', SendleFacade::class);
	}
}