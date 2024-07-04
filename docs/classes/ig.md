# `igeek/utilities`

## `iG` Class

The `iG` class is used as the main class which provides easy API to access any of the registered utility drivers and to register new utility driver.

### Requirements to register a utility driver

The class which you wish to register as a utility driver must implement [`Utility_Driver`](interfaces/utility-driver.md) interface.

### Available Methods
- [`is_driver_registered()`](#is_driver_registered-string--name---bool)
- [`register_driver()`](#register_driver-string--class---void)


### `is_driver_registered( string  $name ) : bool`

This method can be used to check if a driver is already registered with the `iG` class or not.

### `register_driver( string  $class ) : void`

`register_driver()` is used to register a utility driver with the `iG` class.
Let us consider that we have a class `\Friendly_Dogs\Utilities\Games` that we want to register with `iG` class. Our code would look something like this:

```php
<?php
// file: my-project/bootstrap.php

\iG\Utilities\Autoloader::register( '\Friendly_Dogs', __DIR__ . '/src' );

iG::register_driver( \Friendly_Dogs\Utilities\Games::class );
```

```php
<?php
// file: my-project/src/classes/utilities/class-games.php

namespace Friendly_Dogs\Utilities;

use \iG\Utilities\Interfaces\Utility_Driver;

class Games implements Utility_Driver {

    public static function get_driver_name() : string {
        return 'games';
    }

    public static function get_instance() : static {
        return new static();
    }

    public function say_hello() : void {
        echo 'Hello World!';
    }

}
```

```php
<?php
// file: my-project/index.php

require_once __DIR__ . '/bootstrap.php';

iG::games()->say_hello();

```

This allows for easy and quick access to any utility drivers registered with the `iG` class without having to import with long resource names or instantiating them.

All of the bundled utility drivers in the `utilities` directory are pre-registered with `iG` class and ready to be used in similar way.
