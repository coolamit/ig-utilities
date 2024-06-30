# `ig-utilities`

## interface `Utility_Driver`

The `Utility_Driver` interface contains signatures for two methods which must be defined in a class implementing this interface. This interface is required for a class which is to be registered as a utility driver for the [`iG`](../ig) class.

### `public static function get_driver_name() : string`

This `static` method must return a string which will be used as the name to register the class as utility driver with `iG` class. The name must not contain characters which are not allowed in a valid [PHP function name](https://www.php.net/manual/en/functions.user-defined.php).

### `public static function get_instance() : object`

This `static` method must return the instance of the class which implements this `interface`. The class can either implement Singleton Pattern by using the [`Singleton`](../traits/singleton.md) trait or it can implement a [Factory Pattern](https://en.wikipedia.org/wiki/Factory_method_pattern).

Consider the example below:

```php
<?php
namespace My_Project\Inc;

use \iG\Utilities\Interfaces\Utility_Driver;

class My_Class implements Utility_Driver {

    public static function get_driver_name() : string {
        return 'my_class';
    }

    public static function get_instance() : object {
        return new static();
    }

}
```

