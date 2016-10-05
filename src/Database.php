<?php

namespace MySimpleApi;

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
		$this->_connstr = "pgsql:host=$host user=$user password=$pass dbname=$db options='--client_encoding=UTF8'";

		$this->connection = pg_connect("dbname=my-simple-api")
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
	 * @return array
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
	 * @param string $query
	 */
	public function query($query) {
		$result = pg_query($query);
		pg_free_result($result);
	}
}