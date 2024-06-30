# `ig-utilities`

## class `Files`

The `Files` class is used as a utility driver with the `iG` class to provide utilities when working with files on the filesystem. It can be used directly on its own as well.

### `get_instance()`

`Files::get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

### `does_exist( string $file_path ) : bool`

The `Files::does_exist()` allows you to check if a given file path exists and is of a valid file. Usually [`file_exists()`](https://www.php.net/manual/en/function.file-exists.php) is used for that purpose but the problem with that function is that it will return `TRUE` even if the path is that of a directory and not of a file. `Files::does_exist()` improves on that in the sense that it will return `TRUE` only if the path is of an actual file and that file exists on the filesystem at that location.
