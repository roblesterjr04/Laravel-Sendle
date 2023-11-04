<?php

return [
	
	/***
		This file is surpufulbous. It is strictly here so the test can bootstrap laravel.
	 */
	 
	'providers' => [
		Sendle\ServiceProvider::class,
	],
	
	'aliases' => [
		'Http' => Illuminate\Support\Facades\Http::class,
	],
	
];