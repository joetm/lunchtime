<?php

/**
 * DB class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

use \SQLite3;

use LunchTime\Config;

/**
 * DB class
 */
final class DB extends \SQLite3
{

    /**
     * Constructor.
     *
     * @throws Exception If database could not be opened
     */
    public function __construct()
    {
        // load dbfile config
        $dbfile = realpath(__DIR__ . "/../" . Config::get('dbfile'));
        if (!$dbfile) {
            throw new Exception("Could not open database connection", 1);
        }

        $this->open($dbfile);

        $this->initTables();
    }

    /**
     * Initialise the database tables
     *
     * @throws Exception If database tables could not be created
     */
    private function initTables()
    {
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

    /**
     * Close the database connection when done.
     *
     * @param string $sql String with a SQL query
     *
     * @return array $row Result row
     */
    public function queryCache($sql)
    {
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);

        return $row;
    }
}
