<?php
namespace handle;
/**
 * Trait magicMethods
 * @method _configure( array $arguments = [] )
 * @method _initialise( array $arguments = [] )
 * @method _new
 * @method _clear
 */
trait magicMethods {

	/**
	 * @var object
	 */
	public $base;

	/**
	 * @return object
	 */
	public function base() { return $this->base; }

	/**
	 * @var array
	 */
	public $cache;

	/**
	 * @var object
	 */
	public $current;

	/**
	 * @return object
	 */
	public function current() { return $this->current; }

	/**
	 * @var object
	 */
	public $family;

	/**
	 * @return object
	 */
	public function family() { return $this->family; }

	/**
	 * @var string
	 */
	public $loaded = '';

	/**
	 * @var string
	 */
	public $loadingDirectory = '';

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var object
	 */
	public $parent;

	/**
	 * @return object
	 */
	public function parent() { return $this->parent; }

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var array
	 */
	public $settings;

	/**
	 * @param array $arguments
	 */
	public function __construct ( $arguments = [] ) {
		$this->__configure( $arguments );
		$this->__initialise( $arguments );
	}

	/**
	 * @param $property
	 * @return bool
	 */
	public function __get ( $property ) {
		if ( isset( $this->{ $property } ) ) {
			return $this->{ $property };
		} else {
			return $this->__load ( $property );
		}
	}

	/**
	 * @param $method
	 * @param array $arguments
	 * @return bool|mixed
	 */
	public function __call ( $method, $arguments = [] ) {
		if ( $method == 'parent' ) die( $method );
		if ( isset ( $this->{ $method } ) && is_callable( $this->{ $method } ) ) {
			return call_user_func_array( $this->{ $method }, $arguments );
		} else {
			return $this->__load ( $method, $arguments );
		}
	}

	/**
	 * @param $property
	 * @param $contents
	 * @return mixed
	 */
	public function set ( $property, $contents ) {
		return $this->{ $property } = $contents;
	}

	/**
	 * @param $property
	 */
	public function delete ( $property ) {
		unset( $this->{ $property } );
	}

	/**
	 * @param $property
	 * @param $fallback
	 * @return mixed
	 */
	public function getSettings( $property, $fallback ) {
		if ( isset( $this->settings [ $property ] ) ) return $this->settings [ $property ];
		if ( isset( $this->parent->settings [ $property ] ) ) return $this->parent->settings [ $property ];
		if ( isset( $this->family->settings [ $property ] ) ) return $this->family->settings [ $property ];
		if ( isset( $this->base->settings [ $property ] ) ) return $this->base->settings [ $property ];
		return $fallback;
	}

	/**
	 * @var array
	 */
	private $dontLoad = [
		'_check',
		'_configure',
		'_initialise',
		'_new',
		'_clear'
	];

	/**
	 * @param $method
	 * @param array $arguments
	 * @return bool
	 */
	private function __load ( $method, $arguments = [] ) {
		if ( !in_array( $method, $this->dontLoad ) ) {
			if ( !$this->includeLoadingFile( $method ) ) return $this->{ $method } = false;
			$inheritance = [
				'base' => $this->base,
				'family' => $this->family,
				'parent' => $this,
			];
			$methodName = $this->name . '\\' . $method;
			if ( class_exists( $methodName ) ) {
				$this->{ $method } = new $methodName( array_merge_recursive( $inheritance, $arguments ) );
				if ( $this->{ $method }->loadingDirectory == $this->loadingDirectory ) {
					$this->{ $method }->loadingDirectory = $this->{ $method }->settings [ 'loadingDirectory' ] = pathinfo( $this->loaded, PATHINFO_DIRNAME );
				}
			} else $this->{ $method } = false;
			return $this->{ $method };
		}
		return false;
	}

	/**
	 * @param array $arguments
	 * @return null
	 */
	private function __configure ( $arguments = [] ) {
		if ( is_callable( $this->_configure( $arguments ) ) ) return $this->_configure();
		if ( !empty( $arguments ) ) {
			foreach ( $arguments as $property => $argument ) {
				$this->{ $property } = $argument;
			}
		}
		global $settings;
		$this->name = get_class( $this );
		if ( empty( $this->parentName ) && empty( $this->baseName ) ) {
			$this->base = $this;
			unset( $this->cache );
		} else {
			if ( $this->parentName == $this->baseName && empty( $this->familyName ) ) {
				$this->family = $this;
			}
		}
		$this->settings = [];
		if ( isset( $settings [ $this->name ] ) ) $this->settings = $settings [ $this->name ];
		$this->loadingDirectory = $this->getSettings( 'loadingDirectory', pathinfo( __FILE__, PATHINFO_DIRNAME ) );
		return null;
	}

	/**
	 * @param array $arguments
	 * @return null
	 */
	private function __initialise ( $arguments = [] ) {
		if ( is_callable( $this->_initialise( $arguments ) ) ) return $this->_initialise();
		return null;
	}

	/**
	 * @param array $arguments
	 * @return magicMethods
	 */
	public function __new ( $arguments = [] ) {
		$return = new self;
		$return->base = $this->base;
		$return->family = $this->family;
		$return->parent = $this->parent;
		foreach ( $arguments as $key => $value ) {
			$return->{ $key } = $value;
		}
		$return->__configure( $arguments );
		$return->__initialise( $arguments );
		return $return;
	}

	/**
	 * @return null
	 */
	public function __clear () {
		if ( is_callable( $this->_clear() ) ) return $this->_clear();
		$empty = $this->__new();
		foreach ( get_object_vars( $this ) as $propertyName => $property ) {
			if ( isset( $empty->{ $propertyName } ) ) $this->{ $propertyName } = $empty->{ $propertyName };
			else unset( $this->{ $propertyName } );
		}
		return null;
	}

	/**
	 * @param $method
	 * @return bool
	 */
	private function includeLoadingFile ( $method ) {
		$try = [
			$this->loadingDirectory . "/$method/_base.php",
			$this->loadingDirectory . "/$method/$method.php",
			$this->loadingDirectory . "/$method.php"
		];
		foreach ( $try as $file ) {
			if ( file_exists( $file ) ) {
				$this->loaded = $file;
				include_once( $file );
				return true;
			}
		}
		return false;
	}
}