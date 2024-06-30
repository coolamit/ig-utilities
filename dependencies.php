<?php
/**
 * Dependencies for ig-utilities package
 *
 * @author Amit Gupta
 *
 * @since  2021-12-06
 */
require_once iG_UTILITIES_ROOT . '/src/classes/class-autoloader.php';

// Register package's namespace for resource autoloading.
\iG\Utilities\Autoloader::register( '\iG\Utilities', __DIR__ . '/src' );


/*
 * Class aliases
 */
class_alias( \iG\Utilities\iG::class, 'iG' );

//EOF
