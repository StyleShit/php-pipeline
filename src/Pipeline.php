<?php

namespace StyleShit\Pipeline;

use StyleShit\Pipeline\Exceptions\InvalidPipe;

class Pipeline
{
    protected $passable = null;

    protected $method = 'handle';

    protected $pipes = [];

    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    public function through(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    public function via(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function then(\Closure $destination)
    {
        $pipeline = $destination;
        $pipes = array_reverse($this->pipes);

        foreach ($pipes as $pipe) {
            $pipeline = function ($passable) use ($pipe, $pipeline) {
                return $this->invokePipe($pipe, $passable, $pipeline);
            };
        }

        return $pipeline($this->passable);
    }

    public function thenReturn()
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function invokePipe($pipe, $passable, $next)
    {
        if (is_callable($pipe)) {
            return $pipe($passable, $next);
        }

        $method = $this->method;

        // Support tuple of [Class, Method].
        if (is_array($pipe)) {
            $method = $pipe[1];
            $pipe = $pipe[0];
        }

        if (is_object($pipe)) {
            return $pipe->{$method}($passable, $next);
        }

        if (is_string($pipe) && class_exists($pipe)) {
            return (new $pipe)->{$method}($passable, $next);
        }

        throw InvalidPipe::make();
    }
}
