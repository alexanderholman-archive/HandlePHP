<?php
namespace handle\php\database\protocol;
use handle;
/**
 * Class mysql
 * @property handle\php $base
 * @property handle\php\database $family
 * @property handle\php\database\protocol $parent
 * @property mysql $current
 * @method handle\php base
 * @method handle\php\database family
 * @method handle\php\database\protocol parent
 * @method mysql current
 *
 * @package handle\php\database\protocol
 */
class mysql extends handle\php\database\protocol implements handle\php\database\protocol {

	/**
	 * @param string|bool $hostname
	 * @param string|bool $username
	 * @param string|bool $password
	 * @param string|bool $database
	 * @param int|bool    $port
	 * @param int|bool    $socket
	 * @param bool        $new_link
	 * @param int         $client_flags
	 *
	 * @return resource
	 */
	public function connect( $hostname = false, $username = false, $password = false, $database = false, $port = false, $socket = false, $new_link = false, $client_flags = 0 ) {

		return $this->family->connection->current->connection = mysql_connect( $hostname, $username, $password, $new_link, $client_flags );

	}

	/**
	 * @param string        $statement
	 * @param string|bool   $database
	 * @param resource|bool $connection
	 *
	 * @return resource
	 */
	public function query( $statement, $database = false, $connection = false ) {

		return $this->family->connection->current->query = $this->family->connection->current->queries[ base64_encode( $statement ) ] = mysql_db_query( $database?:$this->family->connection->current->database, $statement, $connection?:$this->family->connection->current->connection );

	}

	public function fetch_all( $query = false, $force = false ) {

		$query = $query?:$this->family->connection->current->query;

		if ( !count( $query->rows ) || $force ) {

			$query->rows = [];

			while( $row = mysql_fetch_assoc( $query->results ) ) {

				$query->rows[] = $row;

			}

		}

		return $query->rows;

	}

	public function num_rows( $query = false ) {}

	public function insert_id( $query = false ) {}


}