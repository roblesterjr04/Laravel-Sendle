<?php

use Faker\Factory;

if (!function_exists('fake')) {
	function fake() {
		return Factory::create();
	}
}