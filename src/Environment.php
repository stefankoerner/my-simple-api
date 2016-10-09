<?php

namespace MySimpleApi;

use Dotenv\Dotenv as Dotenv;

class Environment {
	public static function init() {
		$dotenv = new Dotenv(__DIR__);
		$dotenv->load();
	}
}
