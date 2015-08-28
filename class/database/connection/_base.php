<?php
namespace handle\php\database;
use handle;
/**
 * Class protocol
 * @property handle\php $base
 * @property handle\php\database $family
 * @property handle\php\database $parent
 * @property connection $current
 * @method handle\php base
 * @method handle\php\database family
 * @method handle\php\database parent
 * @method connection current
 *
 * @package handle\php\database
 */
class connection extends handle\php\database {

	/**
	 * @var mixed
	 */
	public $connection  = false;

	/**
	 * @var string
	 */
	public $database    = "default";

	/**
	 * @var string
	 */
	public $hostname    = "localhost";

	/**
	 * @var string
	 */
	public $password    = "password";

	/**
	 * @var int
	 */
	public $port        = 3306;

	/**
	 * @var string
	 */
	public $protocol    = "mysqli";

	/**
	 * @var handle\php\database\query
	 */
	public $query;

	/**
	 * @var array
	 */
	public $queries     = [];

	/**
	 * @var int
	 */
	public $socket      = 0;

	/**
	 * @var string
	 */
	public $username    = "username";

}