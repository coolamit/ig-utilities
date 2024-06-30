<?php
/**
 * Class containing utility methods for manipulating strings
 *
 * @author Amit Gupta
 *
 * @since  2022-02-05
 */

namespace iG\Utilities\Utilities;

use ErrorException;
use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Singleton;

class Strings implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'strings';

	}

	/**
	 * Method to add slash at beginning of text string
	 *
	 * @param string $txt
	 *
	 * @return string
	 */
	public function leadingslashit( string $txt ) : string {

		return sprintf( '/%s', $this->unleadingslashit( $txt ) );

	}

	/**
	 * Method to remove slash from beginning of text string
	 *
	 * @param string $txt
	 *
	 * @return string
	 */
	public function unleadingslashit( string $txt ) : string {

		return ltrim( $txt, '/\\' );

	}

	/**
	 * Check if a given string is a valid name or not.
	 * The string must have only alphanumeric letters, underscores, hyphens and spaces.
	 *
	 * @param string $txt
	 *
	 * @return bool
	 */
	public function is_name( string $txt ) : bool {

		// We want to make sure our string has more than
		// just spaces, hyphens and underscores
		$txt = preg_replace( '/[\s\-_]+/i', '', $txt );

		return (bool) ( preg_match( '/^[\w]+$/i', $txt ) );

	}

	/**
	 * This is a multibyte version of str_replace() with a couple of caveats.
	 * Unlike the regular str_replace(), this does not accept an array of subjects/haystacks
	 * and unlike the regular str_replace(), it does not tell the number of replacements done.
	 *
	 * This is multibyte safe.
	 *
	 * @param string|array $search  The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
	 * @param string|array $replace The replacement value that replaces found search values. An array may be used to designate multiple replacements only if search parameter is an array.
	 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
	 *
	 * @return string
	 *
	 * @throws ErrorException
	 */
	public function search_replace( string|array $search, string|array $replace, string $subject ) : string {

		$original_subject = $subject;

		$search  = ( is_numeric( $search ) ) ? (string) $search : $search;
		$replace = ( is_numeric( $replace ) ) ? (string) $replace : $replace;

		if (
			( ! is_string( $search ) && ! is_array( $search ) )
			|| ( ! is_string( $replace ) && ! is_array( $replace ) )
		) {
			throw new ErrorException(
				sprintf(
					/* Translators: placeholders are class and method names */
					__( '%1$s::%2$s() expects "search" and "replace" parameters to be of either String or Array type.', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		if ( ! is_array( $search ) && is_array( $replace ) ) {
			throw new ErrorException(
				sprintf(
					/* Translators: placeholders are class and method names */
					__( '%1$s::%2$s() expects "replace" parameter to be of String type when "search" parameter is of String type.', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		if ( is_array( $search ) && is_array( $replace ) && count( $search ) > count( $replace ) ) {
			throw new ErrorException(
				sprintf(
					/* Translators: placeholders are class and method names */
					__( '%1$s::%2$s() expects "replace" parameter to contain items equal in number to items in "search" parameter.', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		$search  = ( is_array( $search ) ) ? array_values( $search ) : $search;
		$replace = ( is_array( $replace ) ) ? array_values( $replace ) : $replace;

		// Let's deal with array type on search and/or replace.
		// Recursive is the way!!
		if ( is_array( $search ) ) {

			$search_count     = count( $search );
			$is_replace_array = false;

			if ( is_array( $replace ) ) {

				$is_replace_array = true;

				if ( $search_count < count( $replace ) ) {
					$replace = array_slice( $replace, 0, $search_count );
					$replace = array_values( $replace );
				}

			}

			for ( $i = 0; $i < $search_count; $i++ ) {
				$replacement = ( true === $is_replace_array ) ? $replace[ $i ] : $replace;
				$subject     = $this->search_replace( $search[ $i ], $replacement, $subject );
			}

			return $subject;

		}  // end array handling block

		if ( $this->is_unicode( $search ) ) {
			$subject = mb_split( preg_quote( $search ), $subject );
		} else {
			$subject = explode( $search, $subject );
		}

		return implode( $replace, $subject );

	}

	/**
	 * Utility method to check if text string is unicode or not.
	 *
	 * @param string $txt
	 *
	 * @return bool
	 */
	public function is_unicode( string $txt ) : bool {

		if ( Data::get_instance()->is_empty( $txt ) ) {
			return false;
		}

		$txt_encoding = mb_detect_encoding( $txt, [ 'ASCII', 'UTF-8' ], true );  // keep ASCII before UTF-8 to prevent false positives

		return ( 'UTF-8' === $txt_encoding );

	}

	/**
	 * Method to get the number of minutes needed to read given text.
	 *
	 * This is multibyte safe.
	 *
	 * @param string $txt
	 * @param int    $words_per_minute Average reading speed. By default, it considers 200 words per minute as the reading speed.
	 *
	 * @return int
	 */
	public function get_minutes_to_read( string $txt, int $words_per_minute = 200 ) : int {

		$minutes = 0;

		if ( Data::get_instance()->is_empty( $txt ) ) {
			return $minutes;
		}

		$words_per_minute = absint( $words_per_minute );

		return (int) ( floor( $this->get_word_count( $txt ) / $words_per_minute ) );

	}

	/**
	 * Method to get word count from a text. HTML tags and WP Shortcodes are not counted.
	 *
	 * This is multibyte safe.
	 *
	 * @param string $txt
	 *
	 * @return int
	 */
	public function get_word_count( string $txt ) : int {

		if ( Data::get_instance()->is_empty( $txt ) ) {
			return 0;
		}

		$txt = strip_shortcodes( $txt );
		$txt = trim( wp_strip_all_tags( $txt, true ) );

		/*
		 * Using regex here instead of `str_word_count()` because this method
		 * needs to be able to accommodate multibyte strings as well.
		 */
		return (int) preg_match_all( '/[\p{L}\'\-\xC2\xAD]+/uim', preg_quote( $txt, '/' ) );

	}

	/**
	 * Method to sanitize a string to be used as a key/var name.
	 *
	 * This is similar to sanitize_key() but unlike that method
	 * this one accepts only a string as a key, does not change
	 * the case and does not allow hyphens.
	 *
	 * This method is not multibyte compatible and works only with
	 * basic latin characters.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_sanitized_key( string $key ) : string {

		$sanitized_key = '';

		if ( empty( $key ) ) {
			return $sanitized_key;
		}

		$key           = trim( $key );
		$sanitized_key = preg_replace( '/[^A-Za-z0-9_]/', '', $key );

		return $sanitized_key;

	}

	/**
	 * Method to sanitize a string and strip out all HTML except links.
	 *
	 * @param string $pattern
	 * @param array  $link_parts
	 *
	 * @return string
	 *
	 * @throws ErrorException
	 */
	public function get_sanitized_with_links( string $pattern, array $link_parts ) : string {

		if ( empty( $pattern ) || empty( $link_parts ) ) {
			throw new ErrorException(
				sprintf(
					/* Translators: Placeholders are names of class and method. */
					__( '%s::%s() expects non empty parameters.', 'ig-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		$allowed_html = [
			'a' => [
				'href'   => true,
				'title'  => true,
				'rel'    => true,
				'target' => true,
				'class'  => true,
			],
		];

		return vsprintf(
			wp_kses( ent2ncr( $pattern ), $allowed_html ),
			$link_parts
		);

	}

	/**
	 * This method recreates the functionality of FILTER_SANITIZE_STRING
	 * which was deprecated in PHP 8.1.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function get_sanitize_filter_string( string $text ) : string {

		// strip all tags
		$text = wp_strip_all_tags( $text );

		// replacement for FILTER_SANITIZE_STRING
		$text = htmlspecialchars( $text, ENT_NOQUOTES | ENT_SUBSTITUTE );

		/**
		 * HTML entities are converted to entity numbers instead of entity name.
		 * For example: `'` is converted to `&#34;` and not `&quote;`.
		 * Below line of code fixes that issue.
		 *
		 * @see https://stackoverflow.com/questions/64083440/use-php-htmlentities-to-convert-special-characters-to-their-entity-number-rather
		 */
		return str_replace( [ "&quot;", "&#039;" ], [ "&#34;", "&#39;" ], $text );

	}

}  // end class

//EOF
