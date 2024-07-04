# `igeek/utilities`

## `Strings` Class

The `Strings` class is used as a utility driver with the `iG` class to provide utilities when working with data of `string` type. It can be used directly on its own as well.

### Available Methods
- [`get_instance()`](#get_instance)
- [`unleadingslashit()`](#unleadingslashit-string--txt---string)
- [`is_name()`](#is_name-string--txt---bool)
- [`search_replace()`](#search_replace-search-replace-string--subject---string)
- [`get_word_count()`](#get_word_count-string--txt---int)
- [`get_minutes_to_read()`](#get_minutes_to_read-string--txt-int--words_per_minute--200---int)


### `get_instance()`

`get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

### `unleadingslashit( string  $txt ) : string`

This method is similar to the [`untrailingslashit()`](https://developer.wordpress.org/reference/functions/untrailingslashit/) in WordPress. The method from WP removes trailing slash from a string and `unleadingslashit()` removes the leading slashes from a string.

### `is_name( string  $txt ) : bool`

This method provides a quick way to check if a string is a valid name or not. The string must have only alphanumeric letters, underscores, hyphens and spaces. It returns `TRUE` if string is a valid name else it returns `FALSE`.

### `search_replace( $search, $replace, string  $subject ) : string`

This is a multibyte version of [`str_replace()`](https://www.php.net/manual/en/function.str-replace.php) with a couple of caveats. Unlike the regular `str_replace()`, this does not:
- accept an array of subjects/haystacks
- tell the number of replacements done

### `get_word_count( string  $txt ) : int`

This method can be used to get word count from a text. HTML tags and WordPress Shortcodes are ignored in the count. This method works with multibyte strings.

### `get_minutes_to_read( string  $txt, int  $words_per_minute = 200 ) : int`

This method can be used to get the number of minutes needed to read given text. This method works with multibyte strings. By default it assumes a reading speed of 200 words per minute.
