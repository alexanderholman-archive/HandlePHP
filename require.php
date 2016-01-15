<?php

namespace handle;

$defaultSettings = require_once ( 'config/default.php' );

$settings = ( isset( $settings ) ) ? array_merge_recursive( $defaultSettings, $settings ) : $defaultSettings;

require_once( __DIR__ . '/trait/magic_methods/_base.php' );

/**
 * Class php
 * @property php\database $database
 * @method php\database database
 */
class php {

    use magic_methods;

}