<?php
/**
 * Trait to implement the Factory pattern in a class
 *
 * @author Amit Gupta
 *
 * @since  2022-08-04
 */

namespace iG\Utilities\Traits;

trait Factory {

	/**
	 * Method to return a new object of the current class.
	 *
	 * This is a variadic method which is able to accept unspecified
	 * number of arguments. All of those arguments are passed to
	 * the constructor of the class using this Trait as individual arguments.
	 * Whether those arguments are used or not depends on the class using this Trait.
	 *
	 * This method has been set as final intentionally, because it is not meant to be overridden.
	 *
	 * @param array $args
	 *
	 * @return static
	 */
	final public static function get_instance( ...$args ) : static {

		return new static( ...$args );

	}

}  // end trait

//EOF
