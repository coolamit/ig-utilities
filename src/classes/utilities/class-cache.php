<?php
/**
 * Class for providing a better API around wp_cache.
 *
 * @author Amit Gupta
 *
 * @since  2024-06-30
 */

namespace iG\Utilities\Utilities;

use ErrorException;
use Exception;
use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Factory;

class Cache implements Utility_Driver {

	use Factory;

	/**
	 * Default cache group name.
	 *
	 * @var string
	 */
	public const DEFAULT_GROUP = 'ig_utilities_cache_v3';

	/**
	 * Allowed failed attempts to add or update cache.
	 *
	 * @var int
	 */
	public const MAX_FAILED_ATTEMPTS = 5;

	/**
	 * Suffix used for the key that tracks failed attempts count.
	 *
	 * @var string
	 */
	protected const _ATTEMPTS_KEY_SUFFIX = 'attempts_count';

	/**
	 * Value used to indicate non-existing cache.
	 *
	 * @var string
	 */
	protected const _EMPTY_VALUE = 'empty';

	/**
	 * Original cache key before hashing.
	 *
	 * @var string
	 */
	protected string $_original_key = '';

	/**
	 * Key for storing the number of cache priming attempts.
	 *
	 * @var string
	 */
	protected string $_attempts_key = '';

	/**
	 * Hashed cache key.
	 *
	 * @var string
	 */
	protected string $_hashed_key = '';

	/**
	 * Cache group for storing current key data.
	 *
	 * @var string
	 */
	protected string $_cache_group = '';

	/**
	 * Cache expiry time in seconds.
	 * Defaults to 15 minutes.
	 *
	 * @var int
	 */
	protected int $_cache_expiry = 900;

	/**
	 * Cache expiry time on failure in seconds.
	 * Defaults to 2 minutes on failure.
	 *
	 * @var int
	 */
	protected int $_failure_expiry = 120;

	/**
	 * Callback for priming the cache.
	 *
	 * @var callable
	 */
	protected $_cache_callback;

	/**
	 * Parameters for the cache callback.
	 *
	 * @var array
	 */
	protected array $_callback_params;

	/**
	 * Constructor to initialize the cache system.
	 *
	 * @param string $key   Cache key for the request.
	 * @param string $group Cache group for storing data.
	 *
	 * @throws ErrorException
	 */
	public function __construct( string $key, string $group = '' ) {

		if ( empty( $key ) ) {
			throw new ErrorException(
				sprintf(
					/* Translators: %s is PHP Class name */
					__( '%s class requires a non-empty string as a cache key', 'ig-utilities' ),
					static::class
				)
			);
		}

		$this->_initialize_variables( $key, $group );

	}

	/**
	 * Initializes class variables.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return void
	 */
	protected function _initialize_variables( string $key, string $group = '' ) : void {

		$this->_original_key = $key;
		$this->_hashed_key   = md5( $key );
		$this->_attempts_key = md5( sprintf( '%1$s-%2$s', $key, static::_ATTEMPTS_KEY_SUFFIX ) );
		$this->_cache_group  = ( ! empty( $group ) ) ? $group : static::DEFAULT_GROUP;

	}

	/**
	 * Retrieves the driver name of the class.
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'cache';

	}

	/**
	 * Deletes cached data for the current key.
	 *
	 * @return Cache
	 */
	public function delete() : static {

		wp_cache_delete( $this->_hashed_key, $this->_cache_group );
		wp_cache_delete( $this->_attempts_key, $this->_cache_group );

		return $this;

	}

	/**
	 * Sets the cache expiry time for the current key.
	 *
	 * @param int $expiry
	 *
	 * @return Cache
	 */
	public function expires_in( int $expiry ) : static {

		$expiry              = ( 0 < $expiry ) ? $expiry : $this->_cache_expiry;
		$this->_cache_expiry = $this->_get_randomized_expiry( $expiry );

		return $this;

	}

	/**
	 * Sets the cache expiry time on failure.
	 *
	 * @param int $expiry
	 *
	 * @return Cache
	 */
	public function on_failure_expires_in( int $expiry ) : static {

		$expiry                = ( 0 < $expiry ) ? $expiry : $this->_failure_expiry;
		$this->_failure_expiry = $this->_get_randomized_expiry( $expiry );

		return $this;

	}

	/**
	 * Converts expiry duration to a random value to prevent race conditions.
	 *
	 * @param int $expiry
	 *
	 * @return int
	 */
	protected function _get_randomized_expiry( int $expiry = 0 ) : int {

		return ( $expiry + wp_rand( 1, 60 ) );

	}

	/**
	 * Sets the callback for updating the cache.
	 *
	 * @param callable $callback
	 * @param array    $parameters
	 *
	 * @return Cache
	 *
	 * @throws ErrorException
	 */
	public function updates_with( callable $callback, ...$parameters ) : static {

		if ( empty( $callback ) ) {
			throw new ErrorException(
				sprintf(
					/* Translators: 1. is PHP Class name. 2. is PHP class method name. */
					__( '%1$s::%2$s() requires a valid callback', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}
		$this->_cache_callback  = $callback;
		$this->_callback_params = $parameters;

		return $this;

	}

	/**
	 * Retrieves the cache data or primes the cache if not present.
	 *
	 * @return mixed
	 *
	 * @throws ErrorException
	 */
	public function get() : mixed {

		$data = $this->_fetch_cached_data();

		if ( ! $this->_is_empty( $data ) ) {
			return $data;
		}

		if ( empty( $this->_cache_callback ) || ! is_callable( $this->_cache_callback ) ) {
			throw new ErrorException(
				__( 'A valid callback must be set for automatic cache updates.', 'ig-utilities' )
			);
		}

		$failed_attempts = $this->_get_failed_attempts();

		if ( ! $this->_has_exceeded_failed_attempts_limit( $failed_attempts ) ) {

			$data = $this->_get_uncached_data();

			$this->_store_cache( $data );

		} else {
			$data = '';
		}

		return ( $this->_is_empty( $data ) ) ? '' : $data;

	}

	/**
	 * Fetches data from the cache.
	 *
	 * @return mixed
	 */
	protected function _fetch_cached_data() : mixed {

		$found = null;
		$data  = wp_cache_get( $this->_hashed_key, $this->_cache_group, false, $found );

		return ( false === $found ) ? static::_EMPTY_VALUE : $data;

	}

	/**
	 * Checks if the cache data is empty.
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	protected function _is_empty( mixed $data ) : bool {

		return ( static::_EMPTY_VALUE === $data );

	}

	/**
	 * Gets the count of failed attempts to update cache.
	 *
	 * @return int
	 */
	protected function _get_failed_attempts() : int {

		$count = wp_cache_get( $this->_attempts_key, $this->_cache_group );

		return ( empty( $count ) || ! is_numeric( $count ) ) ? 0 : absint( $count );

	}

	/**
	 * Checks if the limit of failed attempts has been exceeded.
	 *
	 * @param int $count
	 *
	 * @return bool
	 */
	protected function _has_exceeded_failed_attempts_limit( int $count ) : bool {

		return ( static::MAX_FAILED_ATTEMPTS < $count );

	}

	/**
	 * Retrieves uncached data from the callback source.
	 *
	 * @return mixed
	 */
	protected function _get_uncached_data() : mixed {

		try {

			$data = call_user_func_array( $this->_cache_callback, $this->_callback_params );

			return ( Data::get_instance()->is_empty( $data ) ) ? static::_EMPTY_VALUE : $data;

		} catch ( Exception $e ) {

			return static::_EMPTY_VALUE;

		}

	}

	/**
	 * Stores data in the cache.
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	protected function _store_cache( mixed $data ) : bool {

		$short_circuit = apply_filters( 'ig_utilities_cache_prevent_save', false, $this->_original_key, $this->_cache_group );

		if ( true === $short_circuit ) {
			return true;
		}

		$expiry = $this->_cache_expiry;

		if ( $this->_is_empty( $data ) ) {

			$failed_attempts = $this->_get_failed_attempts();
			$failed_attempts++;

			$expiry = $this->_failure_expiry;

			if ( $this->_has_exceeded_failed_attempts_limit( $failed_attempts ) ) {
				$failed_attempts = 0;
				$expiry          = $this->_cache_expiry;
			}

			$this->_set_failed_attempts( $failed_attempts );

		}

		return wp_cache_set( $this->_hashed_key, $data, $this->_cache_group, $expiry );

	}

	/**
	 * Sets the failed attempts count.
	 *
	 * @param int $count
	 *
	 * @return void
	 */
	protected function _set_failed_attempts( int $count ) : void {

		wp_cache_set( $this->_attempts_key, $count, $this->_cache_group, $this->_cache_expiry );

	}

}  // end class

//EOF
