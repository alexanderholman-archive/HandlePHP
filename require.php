<?php

namespace handle;

$defaultSettings = require_once ( 'config/default.php' );

$settings = ( isset( $settings ) ) ? array_merge_recursive( $defaultSettings, $settings ) : $defaultSettings;

require_once( './trait/magicMethods/_base.php' );

/**
 * Class php
 * @property php\database $database
 * @method php\database database
 */
class php { use magicMethods; }