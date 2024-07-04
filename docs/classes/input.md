# `igeek/utilities`

## `Input` Class

The `Input` class is used as a utility driver with the `iG` class to provide utilities when working with data input from a post request or URL query-string or fetching a value from Server or Environment vars. It can be used directly on its own as well.

### Available Methods
- [`get_instance()`](#get_instance)


### Usage

PHP has a nice function baked into it by the name of [`filter_input()`](https://www.php.net/manual/en/function.filter-input.php) which not only allows you to fetch data from an input source but also sanitize/filter it at the same time. But one big issue with it is that it does not work on CLI; so if you are writing unit tests your only option is to mock `filter_input()` so that you can write tests for your code which uses that function.

The implementation in `Input` class fixes that by providing a pseudo mock for CLI. The `Input` class also adds syntactical sugar to make the code more readable and intuitive.

Consider following example.

```php
// Fetch name and email from data posted by a form on the browser
$name  = $_POST['name'];
$email = $_POST['email'];

// .... now run data sanitization on these vars

// Or you could do the following
// which will not only get the data but also
// sanitize it at the same time quickly
$name  = iG::input()->post( 'name', FILTER_SANITIZE_STRING );
$email = iG::input()->post( 'email', FILTER_SANITIZE_EMAIL );

// You can also do the following
$name  = Input::post( 'name', FILTER_SANITIZE_STRING );
$email = Input::post( 'email', FILTER_SANITIZE_EMAIL );

// Similarly if you want to get a query-string var
// then you can do either of following
$post_id = iG::input()->get( 'post_id', FILTER_SANITIZE_NUMBER_INT );
$post_id = Input::get( 'post_id', FILTER_SANITIZE_NUMBER_INT );

```

Along the same lines `Input::server()`, `Input::cookie()` and `Input::env()` are also available.

If you are a purist and do not want to use the syntactical sugar, then you can always do something like this.

```php
$post_id = Input::get_instance()->filter( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT )
```
The `Input::filter()` method here is a drop-in replacement for PHP's `filter_input()` with same signature.

### `get_instance()`

`get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

