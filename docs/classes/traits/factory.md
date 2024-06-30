# `ig-utilities`

## trait `Factory`

The `Factory` trait provides an easy and quick way to implement [Factory method pattern](https://en.wikipedia.org/wiki/Factory_method_pattern) in a class. This is useful in WordPress where a class object has to be created programmatically without specifying the `new` keyword or class name every time. Using this pattern on a class provides a method on that class which returns a new object of the class every time it is called.

The `Factory` trait, by implementing Factory Method Pattern, makes sure a new instance of the class is returned by the Factory method every time it is called.

### `get_instance()`

`<CLASSNAME>::get_instance()` method is available on the class which uses the `Factory` trait. This method returns a new instance of that class.

This method is set as `final` which means it cannot be overridden in any child class.

The class can still use its constructor as normal, but since its implementing Factory Method Pattern, the constructor is called every time the Factory method is called.

**Tip:** It's not considered a good practice to put any business logic in a class constructor. Class constructor should be used as a bootstrap function for the class which only calls other class methods and/or assigns values to class variables.
