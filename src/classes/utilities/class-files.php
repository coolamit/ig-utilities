<?php
/**
 * Class containing utility methods for working with files on filesystem
 *
 * @author Amit Gupta
 *
 * @since  2022-02-22
 */

namespace iG\Utilities\Utilities;

use iG\Utilities\Interfaces\Utility_Driver;
use iG\Utilities\Traits\Singleton;
use SplFileObject;

class Files implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {

		return 'files';

	}

	/**
	 * Method to get the number of lines in a file.
	 * This works with large files as well because it does not load whole file in memory at once.
	 *
	 * @param string $file_path
	 *
	 * @return int
	 */
	public function get_number_of_lines( string $file_path ) : int {

		$count = 0;

		if ( ! $this->does_exist( $file_path ) ) {
			return $count;
		}

		$file = new SplFileObject( $file_path, 'r' );

		while ( ! $file->eof() ) {
			$data  = $file->fread( 4096 );
			$count += substr_count( $data, PHP_EOL );
		}

		unset( $file );

		return $count;

	}

	/**
	 * Method to check if provided file path is valid and if a file exists or not.
	 *
	 * @param string $file_path Physical path of file
	 *
	 * @return bool
	 */
	public function does_exist( string $file_path ) : bool {

		return (
			! empty( $file_path )
			&& file_exists( $file_path )
			&& validate_file( $file_path ) === 0
		);

	}

}  // end class

//EOF
