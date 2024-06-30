# `ig-utilities`

## trait `Singleton`

The `Singleton` trait provides an easy and quick way to make any class a [Singleton](https://en.wikipedia.org/wiki/Singleton_pattern). This is useful in WordPress where you might need to use methods of a class in multiple places but do not want to create objects of that class multiple times as it performs certain actions on instantiation which you do not want to do more than once.

Alternate approach to this has been using `Static` classes where one time actions are put in a static method which is called only once and then other class methods can be statically called without running the risk of queuing up one time actions again. Another approach for this in PHP has been to put the class object in a `global` var to prevent instantiating the class again.

The `Singleton` trait, by implementing Singleton Pattern, makes sure a class using it is instantiated only once per request even if it is used in multiple places.

### `get_instance()`

`<CLASSNAME>::get_instance()` method is available on the class which uses the `Singleton` trait. This method returns the `Singleton` object of that class.

This method is set as `final` which means it cannot be overridden in any child class.

This trait sets the constructor as `protected` to prevent direct instantiation of the class using this trait. The class can still use its constructor as normal, but since its implementing Singleton Pattern, the constructor will be run only once when the class is instantiated. This means any code you want to run only once can be put in the class constructor.

**Tip:** It's not considered a good practice to put any business logic in a class constructor. Class constructor should be used as a bootstrap function for the class which only calls other class methods and/or assigns values to class variables.
