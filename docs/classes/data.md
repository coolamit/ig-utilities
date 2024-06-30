# `ig-utilities`

## class `Data`

The `Data` class is used as a utility driver with the `iG` class to provide utilities when working with data of multiple types. It can be used directly on its own as well.

### `get_instance()`

`get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

### `is_empty( mixed $value ) : bool`

This method can be used to determine if a variable is empty or has any data in it. This is slightly different than the [`empty()`](https://www.php.net/manual/en/function.empty.php) function in PHP because that one returns `false` even if a string has nothing but whitespace. So if a string needs to be checked for existence of non-whitespace data, then `is_empty()` can be used for that purpose.

Also, unlike PHP's `empty()` function, `is_empty()` does not return `TRUE` if the data passed to it is a boolean `FALSE` or a numeric `0`.

Consider following examples

```php  
$a = "   \n";  // tab, space and new line  
$b = '  abcd ';  
$c = "  \xc2\xa0 \n";  // tab, unicode whitespace, space and new line  
$d = "  संतरा \n";  // tab, multibyte string, space and new line  
$e = '    '; // only white space
$f = false; // boolean FALSE
$g = 0; // numeric zero
  
var_dump( iG::data()->is_empty( $a ) );  // will return TRUE  
var_dump( iG::data()->is_empty( $b ) );  // will return FALSE  
var_dump( iG::data()->is_empty( $c ) );  // will return TRUE  
var_dump( iG::data()->is_empty( $d ) );  // will return FALSE  
var_dump( iG::data()->is_empty( $e ) );  // will return TRUE
var_dump( iG::data()->is_empty( $f ) );  // will return FALSE
var_dump( iG::data()->is_empty( $g ) );  // will return FALSE
```  

This method takes care of unicode whitespace characters as well and works with multibyte strings too.  
