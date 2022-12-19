# PHP Pipeline
Just a simple Pipeline for PHP, Inspired by [Laravel's API](https://github.com/laravel/framework/blob/master/src/Illuminate/Pipeline/Pipeline.php).


## Usage Example:

A simple usage might look like this:
    
```PHP
class FirstPipe
{
    public function handle($value, \Closure $next)
    {
        return $next($value . ' - first pipe');
    }
}

class SecondPipe
{
    public function handle($value, \Closure $next)
    {
        return $next($value . ' - second pipe');
    }
}

$pipeline = new Pipeline();

$pipeline
    ->send('initial value')
    ->through([
        FirstPipe::class,
        SecondPipe::class,
    ])
    ->then(function ($value) {
        return $value . ' - final value';
    });

// Output: "initial value - first pipe - second pipe - final value"
```

You can also change the method to call for each pipe:

```PHP
$pipeline
    ->send('initial value')
    ->through([
        FirstPipe::class,
        SecondPipe::class,
    ])
    ->via('anotherMethod')
    ->then(function ($value) {
        return $value . ' - final value';
    });
```

While using a class is the cleanest way to create a pipe, there are also other ways to do it:

```PHP
$result = $pipeline
    ->send(1)
    ->through([
        // Use a closure.
        function ($passable, \Closure $next) {
            return $next($passable + 1);
        },

        // Use a class instance.
        new Pipe(),

        // Use a class string.
        Pipe::class,

        // Use a tuple of [Class, Method] with both instance and class string.
        [new Pipe(), 'handle'],
        [Pipe::class, 'anotherMethod'],
    ])
    ->thenReturn();
```


## Available Methods:

`Container::send($passable)` - Set the initial value to be passed through the pipeline.

`Container::through($pipes)` - Set the array of pipes to be called.

`Container::via($method)` - Set the method to call the for each class pipe.

`Container::then($destination)` - Set the final destination callback.

`Container::thenReturn()` - Return the final value without passing it through a destination function.


___
For more information, check out the [tests](./tests/PipelineTest.php).