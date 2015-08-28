<?php
namespace handle\php\database;

/**
 * Interface protocol
 *
 * @package handle\php\database
 */
interface protocol {

	/**
	 * @param string|bool $hostname
	 * @param string|bool $username
	 * @param string|bool $password
	 * @param string|bool $database
	 * @param int|bool    $port
	 * @param int|bool    $socket
	 *
	 * @return mixed
	 */
	public function connect( $hostname = false, $username = false, $password = false, $database = false, $port = false, $socket = false );

	/**
	 * @param string $statement
	 *
	 * @return mixed
	 */
	public function query( $statement );

	/**
	 * @param query|bool $query
	 *
	 * @return mixed
	 */
	public function fetch_all( $query = false );

	/**
	 * @param query|bool $query
	 *
	 * @return mixed
	 */
	public function num_rows( $query = false );

	/**
	 * @param query|bool $query
	 *
	 * @return mixed
	 */
	public function insert_id( $query = false );

}