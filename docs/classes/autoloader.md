# `ig-utilities`

## class `Autoloader`

The `Autoloader` class provides the ability to register any namespace and map it to a directory. It has a specific pattern it follows for file naming and directory structure. It is not PSR compliant; we follow [WordPress coding standards](https://make.wordpress.org/core/handbook/coding-standards/).

- Each `class`, `trait` or `interface` must be in its own file.
- Resource names should follow snake case with each word starting with an uppercase letter. For example:
    - `class My_Dog` is correct.
    - `class My_dog` is ***not*** correct.
    - `class my_Dog` is ***not*** correct.
    - `class my_dog` is ***not*** correct.
    - `class MyDog` is ***not*** correct.
- The file name should be same as resource name with resource type as a prefix.
- Underscores in resource name should be replaced with hyphens in file name.
- File names must be in lowercase letters.
- Consider following examples.
    - Class `Abc` would be in file named `class-abc.php`
    - Class `Lazy_Dog` would be in file named `class-lazy-dog.php`
    - Trait `Lazy_Dog` would be in file named `trait-lazy-dog.php`
    - Interface `Lazy_Dog` would be in file named `interface-lazy-dog.php`
- Class resources should be in the `classes` sub-directory under the root directory to which the namespace is mapped.
    - Other resources (`traits` and `interfaces`) should be in their own sub-directories under the `classes` directory. The directory name must be a plural.

Consider following directory structure:
```
      |_ src
          |_ classes
              |_ class-my-dog.php
              |_ class-neighbour-dog.php
              |_ traits
                  |_ trait-lazy-dog.php
                  |_ trait-clever-dog.php
              |_ interfaces
                  |_ interface-dog.php
```

Now if we want our project's root namespace to be `Friendly_Dogs` then we can map the `src` directory like this in our `bootstrap.php` file (this can be any file) in our root directory where `src` directory resides, to enable resource autoloading.

```php
\iG\Utilities\Autoloader::register( '\Friendly_Dogs', __DIR__ . '/src' );
```

With the namespace registered and mapped to our `src` directory, we can use any of our resources and just referencing them anywhere in our code (after the above line) will load that resource if it isn't already loaded. So we can do something like this and expect it to work.

```php
<?php
namespace Friendly_Dogs;

use \Friendly_Dogs\Traits\Lazy_Dog;
use \Friendly_Dogs\Interfaces\Dog;

class My_Dog implements Dog {

    use Lazy_Dog;

    public function __construct() {
        // some code here
    }

}
```

