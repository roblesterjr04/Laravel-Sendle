<?php

return [
	
	/***
		This file is surpufulbous. It is strictly here so the test can bootstrap laravel.
	 */
	 
	'providers' => [
		Sendle\ServiceProvider::class,
		Illuminate\Filesystem\FilesystemServiceProvider::class,
	],
	
	'aliases' => [
		'Http' => Illuminate\Support\Facades\Http::class,
		'Storage' => Illuminate\Support\Facades\Storage::class,
	],
	
];