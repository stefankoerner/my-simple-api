<?php

namespace MySimpleApi;

class ApartmentService {

	const EMAIL = "
You can edit the apartment details here:
{{link}}

This email is spam? Check the source of this email, search for the domain of the sender and contact the domain owner. 
";

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
	 * @param integer $id
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

		$keys = ['created', 'token'];
		$values = [date("Y-m-d H:i:s"), $this->generateRandomString()];

		$optionalKeys = ["line1", "line2", "street", "no", "country", "zip", "city", "email"];
		foreach ($optionalKeys as $optionalKey) {
			if (isset($data[$optionalKey]) && is_scalar($data[$optionalKey])) {
				$keys[] = $optionalKey;
				$values[] = pg_escape_string($data[$optionalKey]);
			}
		}

		$sql = "INSERT INTO my_simple_api "
			  ."(".implode(", ", $keys).") values ('".implode("', '", $values)."') ";

		// send email
		$id = Database::getInstance()->addItem($sql);
		$token = $values[array_search('token', $keys)];
		$frontend = getenv('FRONTEND_URL') ? : 'http://localhost:4200';
		$link = $frontend . '/apartments/edit/' . $id . '?token=' . $token;
		$message = str_replace('{{link}}', $link, self::EMAIL);
		$email = $values[array_search('email', $keys)];
		@mail($email, "Apartment added", $message, "From: my-simple-api");
	}

	/**
	 * @param integer $id
	 * @param string[] $data
	 */
	public function updateItem($id, $data = []) {

		$keys = [];
		$values = [];
		$optionalKeys = ["line1", "line2", "street", "no", "country", "zip", "city", "email"];
		foreach ($optionalKeys as $optionalKey) {
			$keys[] = $optionalKey;
			if (isset($data[$optionalKey]) && (is_string($data[$optionalKey]) || is_int($data[$optionalKey])) && $data[$optionalKey] !== "") {
				$values[] = "'".pg_escape_string($data[$optionalKey])."'";
			}
			else {
				$values[] = 'NULL';
			}
		}

		if (count($keys) > 0) {
			$sql = "UPDATE my_simple_api "
				  ."SET (".implode(", ", $keys).") = (".implode(", ", $values).") "
				  ."WHERE id = ".pg_escape_string($id)." ";
			Database::getInstance()->query($sql);
		}
	}

	public function deleteItem($id) {
		$sql = "DELETE FROM my_simple_api "
			  ."WHERE id = ".pg_escape_string($id)." ";
		Database::getInstance()->query($sql);
	}

	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}