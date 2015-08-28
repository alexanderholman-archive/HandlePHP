<?php
namespace handle\php\database\protocol;
use handle;
/**
 * Class mysqli
 * @property handle\php $base
 * @property handle\php\database $family
 * @property handle\php\database\protocol $parent
 * @property mysqli $current
 * @method handle\php base
 * @method handle\php\database family
 * @method handle\php\database\protocol parent
 * @method mysqli current
 *
 * @package handle\php\database\protocol
 */
class mysqli extends handle\php\database\protocol implements handle\php\database\protocol {

	/**
	 * @param string|bool $hostname
	 * @param string|bool $username
	 * @param string|bool $password
	 * @param string|bool $database
	 * @param int|bool    $port
	 * @param int|bool    $socket
	 *
	 * @return \mysqli|void
	 */
	public function connect( $hostname = false, $username = false, $password = false, $database = false, $port = false, $socket = false ) {

		return mysqli_connect( $hostname, $username, $password, $database, $port, $socket );

	}

	/**
	 * @param string       $statement
	 * @param bool|\mysqli $connection
	 *
	 * @return bool|\mysqli_result|void
	 */
	public function query( $statement, $connection = false ) {
		return $this->family->connection->current->query = $this->family->connection->current->queries[ base64_encode( $statement ) ] = mysqli_query( $connection?:$this->family->connection->current->connection, $statement );
	}

	/**
	 * @param \mysqli_result|bool $query
	 *
	 * @return array|null|void
	 */
	public function fetch_all( $query = false ) {
		return mysqli_fetch_all( $query?:$this->family->connection->current->query );
	}

	/**
	 * @param \mysqli_result|bool $query
	 *
	 * @return mixed
	 */
	public function num_rows( $query = false ) {

		return mysqli_fetch_all( $query?:$this->family->connection->current->query );

	}

	/**
	 * @param \mysqli_result|bool $query
	 *
	 * @return mixed
	 */
	public function insert_id( $query = false ) {

		return mysqli_insert_id( $query?:$this->family->connection->current->query );

	}

}