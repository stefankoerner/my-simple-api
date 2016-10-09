<?php

namespace MySimpleApi;

use Dotenv\Dotenv as Dotenv;

/**
 * Class Environment
 * Parses the file <i>.env</i> to PhP environment variables
 * @package MySimpleApi
 */
class Environment {
	public static function init() {
		$dotenv = new Dotenv(__DIR__);
		$dotenv->load();
	}
}
