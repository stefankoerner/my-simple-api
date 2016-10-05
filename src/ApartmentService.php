<?php

namespace MySimpleApi;

class ApartmentService {

	/**
	 * @var ApartmentService
	 */
	private static $instance = null;

	/**
	 * @return ApartmentService
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new ApartmentService();
		}
		return self::$instance;
	}

	/**
	 * @param int $page
	 * @param int $limit
	 * @param boolean|string $filter
	 * @return array
	 */
	public function getList($page = 0, $limit = 20, $filter = false) {
		return [];
	}
}