<?php
/**
 * Class containing utility methods for validating and manipulating data.
 *
 * @author Amit Gupta
 *
 * @since  2024-06-30
 */

namespace iG\Utilities\Utilities;

use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Singleton;

class Data implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'data';

	}

	/**
	 * Method to check if a value is empty or not.
	 * This is similar to `empty()` but it does not count whitespace as non-empty string.
	 *
	 * This is multibyte safe.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function is_empty( mixed $value ) : bool {

		if ( is_string( $value ) ) {

			// Strip out all whitespace
			$value = preg_replace( '/\s+/im', '', $value );
			$value = preg_replace( '/[\pZ\pC]+/uim', '', $value );

			// Now we check if there's anything in the string
			return ( 1 > mb_strlen( $value ) );

		}

		return ( empty( $value ) && false !== $value && 0 !== $value );

	}

}  // end class

//EOF
