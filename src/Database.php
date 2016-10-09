<?php

namespace MySimpleApi;


/**
 * Class Database
 * The interface to the local PostgreSQL database.
 * @package MySimpleApi
 */
class Database {

	/**
	 * @var Database
	 */
	private static $instance = null;

	/**
	 * @var resource
	 */
	private $connection = null;

	public function __construct(){
		$this->connection = pg_connect("host=127.0.0.1 user=postgres")
			or die('Could not connect to database ' . pg_last_error());
	}

	public function __destruct() {
		pg_close($this->connection);
	}

	/**
	 * @return Database
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Database();
		}
		return self::$instance;
	}

	/**
	 * @param string $query
	 * @return array a list of assoc arrays
	 */
	public function getList($query) {
		$list = [];
		$result = pg_query($query) or die('Could not send request ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$list[] = $line;
		}
		pg_free_result($result);
		return $list;
	}

	/**
	 * @param string $query query
	 * @return array a single row
	 */
	public function getItem($query) {
		$result = pg_query($query) or die('Could not send request ' . pg_last_error());
		$item = pg_fetch_array($result, null, PGSQL_ASSOC);
		pg_free_result($result);
		return $item;
	}

	/**
	 * @param string $query query
	 */
	public function query($query) {
		$result = pg_query($query);
		pg_free_result($result);
	}

	/**
	 * @param string $query query
	 * @return string insert id
	 */
	public function addItem($query) {
		$query_last_id = "SELECT max(id) as lastId FROM my_simple_api";
		$result = pg_query($query.';'.$query_last_id.';');
		$row = pg_fetch_array($result,0,PGSQL_ASSOC);
		$id = isset($row['lastid']) ? $row['lastid'] : false;
		pg_free_result($result);
		return $id;
	}
}