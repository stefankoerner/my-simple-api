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
	 * @param boolean|string[] $filter
	 * @return array
	 */
	public function getList($page = 0, $limit = 20, $filter = false) {

		if (!is_numeric($page) || !is_numeric($limit)) {
			return [];
		}

		if (is_string($page) && strpos($page, 'e') !== false) {
			return [];
		}

		if (is_string($limit) && strpos($limit, 'e') !== false) {
			return [];
		}

		$sql = "SELECT msa.* FROM my_simple_api msa ";

		if (is_array($filter) && count($filter) > 0) {
			$sql_filter = [];
			foreach ($filter as $key => $val) {
				if (is_string($key) && is_string($val)) {
					$sql_filter[] = "msa.".$key." LIKE '%".pg_escape_string($val)."%'";
				}
			}
			if (count($sql_filter) > 0) {
				$sql .= "WHERE ".implode(" && ", $sql_filter)." ";
			}
		}
		$sql .= "ORDER BY msa.id DESC ";
		$sql .= "LIMIT ".$limit." OFFSET ". $limit * $page;

		return Database::getInstance()->getList($sql);
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function getItem($id) {

		if (!is_numeric($id)) {
			return [];
		}

		if (is_string($id) && strpos($id, 'e') !== false) {
			return [];
		}

		$sql = "SELECT msa.* FROM my_simple_api msa "
			  ."WHERE id = ".pg_escape_string($id)." ";

		return Database::getInstance()->getItem($sql);
	}

	/**
	 * @param string[] $data
	 */
	public function addItem($data = []) {

		$keys = ['created'];
		$values = [date("Y-m-d H:i:s")];

		$optionalKeys = ["line1", "line2", "street", "no", "country", "zip", "city", "email"];
		foreach ($optionalKeys as $optionalKey) {
			if (isset($data[$optionalKey]) && is_string($data[$optionalKey])) {
				$keys[] = $optionalKey;
				$values[] = pg_escape_string($data[$optionalKey]);
			}
		}

		$sql = "INSERT INTO my_simple_api "
			  ."(".implode(", ", $keys).") values ('".implode("', '", $values)."') ";
	}
}