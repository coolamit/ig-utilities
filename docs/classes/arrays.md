# `igeek/utilities`

## `Arrays` Class

The `Arrays` class is available as a utility driver with the `iG` class to provide utility methods to work with arrays. It can be used directly on its own as well.

### Available Methods
- [`get_instance()`](#get_instance)
- [`inhect()`](#inject-to_inject-int--position-array--inject_into---array)
- [`is_associative()`](#is_associative-array--data---bool)
- [`merge_selective()`](#merge_selective-array-arrays---array)


### `get_instance()`

`get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

### `inject( $to_inject, int  $position, array  $inject_into ) : array`

The `inject()` method allows you to inject a value in an array at a specific position (as in the array - considering array numeric positions start from `0`). This is different from [`array_splice()`](https://www.php.net/manual/en/function.array-splice.php) (which is the function which sometimes comes to mind for this task) which replaces the data in an array with something else.

Let us consider the following example:

```php
$data = [
    'bike',
    'jet',
    'yacht',
];

$data = iG::arrays()->inject( 'car', 1, $data );

var_dump( $data );

// Following will be the result
// array(
//     0 => 'bike',
//     1 => 'car',
//     2 => 'jet',
//     3 => 'yacht',
// )
```

### `is_associative( array  $data ) : bool`

The `is_associative()` method allows you to quickly check if a given array is an associative array or not. If an array has even one numeric index, the method will return `FALSE`. The method will return `TRUE` as long as all indices in the array (being checked) are non-numeric.

### `merge_selective( array ...$arrays ) : array`

The `merge_selective()` method allows merging of multiple arrays. This is similar to [`array_merge()`](https://www.php.net/manual/en/function.array-merge.php) with a small difference. In this method, the first array passed is considered the main array in which all subsequent arrays are merged and so the data is merged only for the indices/keys present in the first array and the remaining indices/keys are discarded.

Consider the following example.

```php
$a1 = [
    'bike' => 'BMW',
    'car'  => 'Lamborghini',
    'jet'  => 'Bombardier',
];

$a2 = [
    'bike'  => 'Ducati',
    'car'   => 'McLaren',
    'yacht' => 'Oceanco',
];

$a3 = [
    'jet'   => 'Gulfstream',
    'yacht' => 'Heesen',
];

$a4 = [
    'yacht'      => 'Amels',
    'helicopter' => 'Sikorsky',
];

$a = iG::arrays()->merge_selective( $a1, $a2, $a3, $a4 );

var_dump( $a );

// Following will be the result
// array(
//     'bike' => 'Ducati',
//     'car'  => 'McLaren',
//     'jet'  => 'Gulfstream',
// )

```

