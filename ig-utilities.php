<?php
/*
Plugin Name: iG Utilities
Plugin URI: https://igeek.info/
Description: Collection of utility code/libraries for use with WordPress plugins/themes.
Version: 1.0.0
Author: Amit Gupta
License: GPL v2
*/

define( 'iG_UTILITIES_ROOT', __DIR__ );
define( 'iG_UTILITIES_VERSION', '1.0.0' );

function ig_utilities_loader() : void {
	require_once iG_UTILITIES_ROOT . '/dependencies.php';
}

ig_utilities_loader();

//EOF
