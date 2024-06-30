<?php
/**
 * Class containing utility methods for manipulating arrays
 *
 * @author Amit Gupta
 *
 * @since  2022-01-18
 */

namespace iG\Utilities\Utilities;

use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Singleton;

class Arrays implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'arrays';

	}

	/**
	 * Method to inject value in an array at a specific position.
	 *
	 * @param mixed $to_inject
	 * @param int   $position
	 * @param array $inject_into
	 *
	 * @return array
	 */
	public function inject( mixed $to_inject, int $position, array $inject_into ) : array {

		$before = [];
		$after  = $inject_into;

		if ( 0 < $position ) {
			$before = array_slice( $inject_into, 0, $position );
			$after  = array_slice( $inject_into, $position );
		}

		return array_merge(
			$before,
			[ $to_inject ],
			$after
		);

	}

	/**
	 * Method to check if an array is associative array or not.
	 * It returns TRUE even if there is a single non-numeric
	 * index in the array.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function is_associative( array $data ) : bool {

		if ( empty( $data ) ) {
			return false;
		}

		$numeric_indices = count(
			array_filter(
				array_keys( $data ),
				'is_numeric'
			)
		);

		return ( 1 > $numeric_indices );

	}

	/**
	 * Method to check if the passed array has any empty index.
	 * This works reliably only on single dimension arrays having string values
	 * or values which can be evaluated using the `empty()` function.
	 * That is why this method will not work with boolean FALSE, numeric zero, etc.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function has_empty_indices( array $data ) : bool {

		$empty_indices = count(
			array_filter(
				$data,
				[ $this, '_is_value_empty' ]
			)
		);

		return ( 0 < $empty_indices );

	}

	/**
	 * This is same as array_merge() with the only difference is that keys of only
	 * the first array (in which all subsequent arrays are merged) are kept. Any additional
	 * keys from the subsequent array(s) are ignored.
	 *
	 * @param array $arrays Variable list of arrays to merge.
	 *
	 * @return array
	 */
	public function merge_selective( array ...$arrays ) : array {

		$merged = [];

		if ( empty( $arrays ) ) {
			return $merged;
		}

		$arrays = array_values( $arrays );

		if ( 2 > count( $arrays ) ) {

			// Only one array has been passed.
			// Return it as is and bail out.
			return $arrays[ 0 ];

		}

		$merged = array_shift( $arrays );
		$arrays = array_values( $arrays );
		$keys   = array_keys( $merged );

		$arrays_count = count( $arrays );
		$keys_count   = count( $keys );

		for ( $i = 0; $i < $arrays_count; $i++ ) {

			$current = $arrays[ $i ];

			for ( $j = 0; $j < $keys_count; $j++ ) {

				$key = $keys[ $j ];

				if ( isset( $current[ $key ] ) ) {
					$merged[ $key ] = $current[ $key ];
				}

			}  // end for keys

		}  // end for arrays

		return $merged;

	}

	/**
	 * Wrapper around `empty()` because passing it directly to the `array_filter()` does not work.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function _is_value_empty( mixed $value ) : bool {

		return empty( $value );

	}

}  // end class

//EOF
