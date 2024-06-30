<?php
/**
 * Class to provide toolkit for manipulating time.
 *
 * @author Amit Gupta
 *
 * @since  2022-11-23
 */

namespace iG\Utilities\Utilities;

use ErrorException;
use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Singleton;

class Time implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'time';

	}

	/**
	 * Method to get human-readable time difference between two timestamps.
	 *
	 * @param int $from
	 * @param int $to
	 *
	 * @return string
	 *
	 * @throws ErrorException
	 */
	public function get_human_readable_diff( int $from, int $to ) : string {

		if ( 0 > $from || 1 > $to ) {
			throw new ErrorException(
				sprintf(
					/* Translators: Placeholders are for class and method name. */
					__( '%1$s::%2$s() expects valid timestamps to calculate difference.', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		$readable_diff = [];
		$diff          = (int) ( $to - $from );

		if ( WEEK_IN_SECONDS <= $diff ) {

			$weeks = floor( $diff / WEEK_IN_SECONDS );
			$diff  = ( $diff % WEEK_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* Translators: Time difference between two dates, in weeks. %s: Number of weeks. */
				_n( '%s week', '%s weeks', $weeks, 'ig-utilities' ),
				$weeks
			);

		}

		if ( DAY_IN_SECONDS <= $diff ) {

			$days = floor( $diff / DAY_IN_SECONDS );
			$diff = ( $diff % DAY_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* Translators: Time difference between two dates, in days. %s: Number of days. */
				_n( '%s day', '%s days', $days, 'ig-utilities' ),
				$days
			);

		}

		if ( HOUR_IN_SECONDS <= $diff ) {

			$hours = floor( $diff / HOUR_IN_SECONDS );
			$diff  = ( $diff % HOUR_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* Translators: Time difference between two dates, in hours. %s: Number of hours. */
				_n( '%s hour', '%s hours', $hours, 'ig-utilities' ),
				$hours
			);

		}

		if ( MINUTE_IN_SECONDS <= $diff ) {

			$minutes = floor( $diff / MINUTE_IN_SECONDS );
			$diff    = ( $diff % MINUTE_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* Translators: Time difference between two dates, in minutes. %s: Number of minutes. */
				_n( '%s minute', '%s minutes', $minutes, 'ig-utilities' ),
				$minutes
			);

		}

		if ( MINUTE_IN_SECONDS > $diff ) {

			$seconds = $diff;

			$readable_diff[] = sprintf(
				/* Translators: Time difference between two dates, in seconds. %s: Number of seconds. */
				_n( '%s second', '%s seconds', $seconds, 'ig-utilities' ),
				$seconds
			);

		}

		return implode( ' ', $readable_diff );

	}

}  // end class

//EOF
