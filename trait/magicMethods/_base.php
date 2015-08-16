<?php

	namespace handle;

	/**
	 * Class handleFramework
	 */
	trait magicMethods {

		public $base;
		public $baseName = '';
		public $cache;
		public $className = '';
		public $current;
		public $family;
		public $familyName = '';
		public $loaded = '';
		public $loadingDirectory = '';
		public $memory;
		public $parent;
		public $parentName = '';
		public $path;
		public $settings;
		public $version;

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
					'parentName' => $this -> className
				];
				$classExists = false;
				if ( class_exists( 'handle' . ucwords( $method ) ) ) {
					$methodName = 'handle' . ucwords( $method );
					$classExists = true;
				}
				if ( !$classExists && class_exists( $method ) ) {
					$methodName = $method;
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
		private function __check () {
			if ( is_callable( $this -> _check() ) ) return $this -> _check();
			return null;
		}
		private function __configure ( $arguments = [] ) {
			if ( is_callable( $this -> _configure( $arguments ) ) ) return $this -> _configure();
			if ( !empty( $arguments ) ) {
				foreach ( $arguments as $property => $argument ) {
					$this -> { $property } = $argument;
				}
			}
			global $settings;
			$this -> className = get_class( $this );
			if ( empty( $this -> parentName ) && empty( $this -> baseName ) ) {
				$this -> base = $this;
				$this -> baseName = $this -> className;
                unset( $this -> cache );
			} else {
				if ( $this -> parentName == $this -> baseName && empty( $this -> familyName ) ) {
					$this -> family = $this;
					$this -> familyName = $this -> className;
				}
			}
			$this -> settings = [];
			$this -> memory = new stdClass();
			if ( isset( $settings [ 'version' ] [ $this -> className ] ) ) $this -> version = $settings [ 'version' ] [ $this -> className ];
			else unset( $this -> version );
			if ( isset( $settings [ $this -> className ] ) ) $this -> settings = $settings [ $this -> className ];
			$this -> loadingDirectory = $this -> getSettings( 'loadingDirectory', pathinfo( __FILE__, PATHINFO_DIRNAME ) );
            /*if ( $this -> className != 'handleCache' ) $this -> cache = $this -> base -> cache -> __new();*/
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
		public function getMethodVersion( $method ) {
			return isset( $settings [ 'version' ] [ $method ] ) ? $settings [ 'version' ] [ $method ] : ( isset( $settings [ 'version' ] [ 'handle' . ucwords( $method ) ] ) ? $settings [ 'version' ] [ 'handle' . ucwords( $method ) ] : ( isset( $settings [ 'version' ] [ 'default' ] ) ? $settings [ 'version' ] [ 'default' ] : false ) );
		}
		private function getFromMemory ( $method, $arguments = [] ) {
			$key = sha1 ( $method . serialize ( $arguments ) );
			if ( isset( $this->memory->{$key} ) ) return $this->memory->{$key};
			return $this->memory->{$key} = call_user_func_array ( $this->{$method}, $arguments );
		}
		private function includeLoadingFile ( $method ) {
			global $settings;
			$version = $this -> getMethodVersion( $method );
			$try = [
                $this -> loadingDirectory . "/$method/$version/_base.php",
				$this -> loadingDirectory . "/$method/$version/$method.php",
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