<?php
namespace handle;
/**
 * Class handleFramework
 */
trait magicMethods {

	public $base;
	public function base() { return $this -> base; }
	public $cache;
	public $current;
	public function current() { return $this -> current; }
	public $family;
	public function family() { return $this -> family; }
	public $loaded = '';
	public $loadingDirectory = '';
	public $name = '';
	public $parent;
	public function parent() { return $this -> parent; }
	public $path;
	public $settings;

	public function __construct ( $arguments = [] ) {
		$this -> __configure( $arguments );
		$this -> __initialise( $arguments );
	}
	public function __get ( $property ) {
		if ( isset( $this -> { $property } ) ) {
			return $this -> { $property };
		} else {
			return $this -> __load ( $property );
		}
	}
	public function __call ( $method, $arguments = [] ) {
		if ( $method == 'parent' ) die( $method );
		if ( isset ( $this -> { $method } ) && is_callable( $this -> { $method } ) ) {
			return call_user_func_array( $this -> { $method }, $arguments );
		} else {
			return $this -> __load ( $method, $arguments );
		}
	}
	public function set ( $property, $contents ) {
		return $this -> { $property } = $contents;
	}
	public function delete ( $property ) {
		unset( $this -> { $property } );
	}
	public function getSettings( $property, $fallback ) {
		if ( isset( $this -> settings [ $property ] ) ) return $this -> settings [ $property ];
		if ( isset( $this -> parent -> settings [ $property ] ) ) return $this -> parent -> settings [ $property ];
		if ( isset( $this -> family -> settings [ $property ] ) ) return $this -> family -> settings [ $property ];
		if ( isset( $this -> base -> settings [ $property ] ) ) return $this -> base -> settings [ $property ];
		return $fallback;
	}

	private $dontLoad = [
		'_check',
		'_configure',
		'_initialise',
		'_new',
		'_clear'
	];

	private function __load ( $method, $arguments = [] ) {
		if ( !in_array( $method, $this -> dontLoad ) ) {
			if ( !$this -> includeLoadingFile( $method ) ) return $this -> { $method } = false;
			$inheritance = [
				'base' => $this -> base,
				'baseName' => $this -> baseName,
				'family' => $this -> family,
				'familyName' => $this -> familyName,
				'parent' => $this,
				'parentName' => $this -> name
			];
			$classExists = false;
			if ( class_exists( $this -> name . '\\' . $method ) ) {
				$methodName = $this -> name . '\\' . $method;
				$classExists = true;
			}
			if ( $classExists ) {
				$this -> { $method } = new $methodName( array_merge_recursive( $inheritance, $arguments ) );
				if ( $this -> { $method } -> loadingDirectory == $this -> loadingDirectory ) {
					$this -> { $method } -> loadingDirectory = $this -> { $method } -> settings [ 'loadingDirectory' ] = pathinfo( $this -> loaded, PATHINFO_DIRNAME );
				}
			} else $this -> { $method } = false;
			return $this -> { $method };
		}
		return false;
	}
	private function __configure ( $arguments = [] ) {
		if ( is_callable( $this -> _configure( $arguments ) ) ) return $this -> _configure();
		if ( !empty( $arguments ) ) {
			foreach ( $arguments as $property => $argument ) {
				$this -> { $property } = $argument;
			}
		}
		global $settings;
		$this -> name = get_class( $this );
		if ( empty( $this -> parentName ) && empty( $this -> baseName ) ) {
			$this -> base = $this;
			$this -> baseName = $this -> name;
			unset( $this -> cache );
		} else {
			if ( $this -> parentName == $this -> baseName && empty( $this -> familyName ) ) {
				$this -> family = $this;
				$this -> familyName = $this -> name;
			}
		}
		$this -> settings = [];
		if ( isset( $settings [ $this -> name ] ) ) $this -> settings = $settings [ $this -> name ];
		$this -> loadingDirectory = $this -> getSettings( 'loadingDirectory', pathinfo( __FILE__, PATHINFO_DIRNAME ) );
		/*if ( $this -> name != 'handleCache' ) $this -> cache = $this -> base -> cache -> __new();*/
		return null;
	}
	private function __initialise ( $arguments = [] ) {
		if ( is_callable( $this -> _initialise( $arguments ) ) ) return $this -> _initialise();
		return null;
	}
	public function __new ( $arguments = [] ) {
		$return = new self;
		$return -> base = $this -> base;
		$return -> baseName = $this -> baseName;
		$return -> family = $this -> family;
		$return -> familyName = $this -> familyName;
		$return -> parent = $this -> parent;
		$return -> parentName = $this -> parentName;
		foreach ( $arguments as $key => $value ) {
			$return -> $key = $value;
		}
		$return -> __configure( $arguments );
		$return -> __initialise( $arguments );
		return $return;
	}
	public function __clear () {
		if ( is_callable( $this -> _clear() ) ) return $this -> _clear();
		$empty = $this -> __new();
		foreach ( get_object_vars( $this ) as $propertyName => $property ) {
			if ( isset( $empty -> { $propertyName } ) ) $this -> { $propertyName } = $empty -> { $propertyName };
			else unset( $this -> { $propertyName } );
		}
		return null;
	}
	private function includeLoadingFile ( $method ) {
		$try = [
			$this -> loadingDirectory . "/$method/_base.php",
			$this -> loadingDirectory . "/$method/$method.php",
			$this -> loadingDirectory . "/$method.php"
		];
		foreach ( $try as $file ) {
			if ( file_exists( $file ) ) {
				$this -> loaded = $file;
				include_once( $file );
				return true;
			}
		}
		return false;
	}
}