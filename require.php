<?php

namespace handle;

$defaultSettings = require_once ( '.settings/default.php' );

$settings = ( isset( $settings ) ) ? array_merge_recursive( $defaultSettings, $settings ) : $defaultSettings;

require_once( './trait/magicMethods/' . ( isset( $settings [ 'version' ] [ '_base' ] ) ? $settings [ 'version' ] [ '_base' ] : $settings [ 'version' ] [ 'default' ] ) . '/_base.php' );

/**
 * Class php
 * @method php\database database
 */
class php { use magicMethods; }