<?php

namespace StyleShit\Pipeline\Exceptions;

class InvalidPipe extends \Exception
{
    public static function make()
    {
        return new static('The pipe must be a callable or an object.');
    }
}
