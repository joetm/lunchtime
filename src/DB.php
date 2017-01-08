<?php

namespace LunchTime;

use LunchTime\Config;


class DB extends \SQLite3
{

	function __construct()
	{
		// load dbfile config
		$dbfile = Config::get('dbfile');
		if (!$dbfile) {
			throw new Exception("Could not open database connection", 1);
		}

		$this->open($dbfile);

		$this->initTables();

	}

	private function initTables() {
		//
		// set up the DB table, if it does not exist
		$sql = "CREATE TABLE IF NOT EXISTS translations (
			  key   TEXT PRIMARY KEY  NOT NULL,
			  value TEXT              NOT NULL
		  );
		";
		$response = $this->exec($sql);
		if (!$response) {
			throw new Exception($this->db->lastErrorMsg(), 1);
		} else {
            echo "Table `translations` created successfully" . PHP_EOL;
		}
	}

    /**
     * Close the database connection when done.
     */
    public function __destruct()
    {
		$this->close();
    }

    public function query($sql) {
    	//
		$result = $this->query($sql);
		if (!$result) {
			return false;
		}

		$row = $result->fetchArray(SQLITE3_ASSOC);
		return $row;
    }

}
