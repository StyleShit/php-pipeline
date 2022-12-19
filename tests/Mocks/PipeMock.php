<?php

namespace StyleShit\Pipeline\Tests\Mocks;

class PipeMock
{
    public function handle($passable, $next)
    {
        return $next($passable + 1);
    }

    public function addTwo($passable, $next)
    {
        return $next($passable + 2);
    }
}
