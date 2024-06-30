# `ig-utilities`

## class `Cache`

The `Cache` class is used as a utility driver with the `iG` class to provide an enhanced API for managing data caching in WordPress, allowing for automatic cache management and data retrieval with failover mechanisms.

This class uses `wp_cache` functions behind the scenes and automatically takes care of quicker updates on failures, expiry time collisions, etc. `wp_cache` must be set up and configured for use before this class can be used. This class does not provide a caching mechanism of its own.

### `get_instance( string $key, string $group = '' )`

This method is available on the `Cache` class to get an instance of it. Since this class uses the `Factory` trait, using this method returns a new object of the class every time this method is called.

This method accepts 2 parameters: a unique key as a string to identify cache instance and a cache group name as a string. The cache group name is optional, if it is not passed then the `Cache` class uses its default group.

### Usage

This class can be used directly or as a driver via the `iG` class.

Consider the following class which provides data for postcards:

```php
use iG\Utilities\Traits\Factory;

class Postcard {

    use Factory;

    public function get( int $id ) : array {

        $post = get_post( $id );

        return [
            'title'   => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'author'  => get_the_author_meta( 'display_name', $post->post_author ),
            'image'   => wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ),
        ];

    }

}
```

And now this class is to be used to fetch data for postcards but that data is to be cached as well so that the database is not hit on every call.

#### Direct Usage

Below is an example of using `Cache` class directly:

```php
use iG\Utilities\Traits\Factory;
use iG\Utilities\Utilities\Cache;

class Postcards {

    use Factory;

    public function display( array $ids ) : void {

        foreach( $ids as $id ) {

            $key  = sprintf( 'postcard-%d', $id );
            $data = Cache::get_instance( $key, 'postcards' )
                          ->expires_in( 3600 )
                          ->on_failure_expires_in( 120 )
                          ->updates_with( [ Postcard::get_instance(), 'get' ], $id )
                          ->get();

            render( 'postcards' )->with( $data );

        }

    }

}
```

#### Usage as `iG` driver

Below is an example of using `Cache` class as driver of `iG` class:

```php
use iG\Utilities\Traits\Factory;

class Postcards {

    use Factory;

    public function display( array $ids ) : void {

        foreach( $ids as $id ) {

            $key  = sprintf( 'postcard-%d', $id );
            $data = iG::cache( $key, 'postcards' )
                       ->expires_in( 3600 )
                       ->on_failure_expires_in( 120 )
                       ->updates_with( [ Postcard::get_instance(), 'get' ], $id )
                       ->get();

            render( 'postcards' )->with( $data );

        }

    }

}
```

### `expires_in( int $expiry ) : static`

This method is used to set the time period after which the cache expires and data has to be fetched again from the specified datasource. It accepts an integer denoting time in seconds and returns the current `Cache` instance to allow for method chaining.

### `on_failure_expires_in( int $expiry ) : static`

This method is used to set the time period after which the cache expires in case cache update fails and the specified datasource does not return any data. It accepts an integer denoting time in seconds and returns the current `Cache` instance to allow for method chaining.

Using this method is optional. If this method is not called then the `Cache` class uses its default failure expiry time in case of cache update failure.

### `updates_with( callable $callback, ...$parameters ) : static`

This method is used to set a callback which provides the data that is to be cached. The first parameter must be a valid callback and then any parameters passed after that are passed to the callback specified in the first parameter.

The callback specified in the first parameter on this method must return data. If it outputs data to buffer then the `Cache` class will not be able to capture that.

It returns the current `Cache` instance to allow for method chaining.

### `get() : mixed`

This method returns the data from the cache if it exists and the cache has not expired. If the cache has expired then it will update the cache using the datasource set via `updates_with()` method and then return that data.

This method must be called after the other methods used to set the cache expiry, cache update, etc. have been called. No other methods of `Cache` class can be chained after this method.

### `delete() : static`

This method can be used to delete data from a cache instance.

It returns the current `Cache` instance to allow for method chaining.
