<?php
/**
 * Interface that every utility driver class has to implement.
 *
 * @author Amit Gupta
 *
 * @since  2022-02-23
 */

namespace iG\Utilities\Interfaces;

interface Utility_Driver {

	/**
	 * Method that provides the name for the driver class
	 * with which it will be associated and called.
	 *
	 * @return string
	 */
	public static function get_driver_name() : string;

	/**
	 * Method which the driver class should have to provide
	 * an instance of itself. It can either implement Singleton
	 * pattern or Factory pattern.
	 *
	 * @param array ...$args
	 *
	 * @return static
	 */
	public static function get_instance( ...$args ) : static;

}  // end interface

//EOF
