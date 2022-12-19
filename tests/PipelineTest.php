<?php

namespace StyleShit\Pipeline\Tests;

use StyleShit\Pipeline\Exceptions\InvalidPipe;
use StyleShit\Pipeline\Pipeline;
use StyleShit\Pipeline\Tests\Mocks\PipeMock;

it('should run an empty pipeline', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->then(function ($passable) {
            return $passable + 2;
        });

    // Assert.
    expect($result)->toBe(3);
});

it('should run a pipeline using a function', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            function ($passable, $next) {
                return $next($passable + 1);
            },
        ])
        ->thenReturn();

    // Assert.
    expect($result)->toBe(2);
});

it('should run a pipeline using a class instance', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            new PipeMock(),
        ])
        ->thenReturn();

    // Assert.
    expect($result)->toBe(2);
});

it('should run a pipeline using a class instance via specific method', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            new PipeMock(),
        ])
        ->via('addTwo')
        ->thenReturn();

    // Assert.
    expect($result)->toBe(3);
});

it('should run a pipeline using a class string', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            PipeMock::class,
        ])
        ->thenReturn();

    // Assert.
    expect($result)->toBe(2);
});

it('should run a pipeline using a class string via specific method', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            PipeMock::class,
        ])
        ->via('addTwo')
        ->thenReturn();

    // Assert.
    expect($result)->toBe(3);
});

it('should run a pipeline using an array', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            [PipeMock::class, 'handle'],
            [new PipeMock(), 'addTwo'],
        ])
        ->thenReturn();

    // Assert.
    expect($result)->toBe(4);
});

it('should run a pipeline through multiple pipe types', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act.
    $result = $pipeline
        ->send(1)
        ->through([
            function ($passable, $next) {
                return $next($passable + 1);
            },
            new PipeMock(),
            PipeMock::class,
            [PipeMock::class, 'handle'],
            [new PipeMock(), 'addTwo'],
        ])
        ->thenReturn();

    // Assert.
    expect($result)->toBe(7);
});

it('should run the pipes top-to-bottom', function () {
    // Arrange.
    $pipeline = new Pipeline();
    $runs = [];

    // Act.
    $pipeline
        ->send(1)
        ->through([
            function ($passable, $next) use (&$runs) {
                $runs[] = 1;

                return $next($passable + 1);
            },
            function ($passable, $next) use (&$runs) {
                $runs[] = 2;

                return $next($passable + 1);
            },
            function ($passable, $next) use (&$runs) {
                $runs[] = 3;

                return $next($passable + 1);
            },
        ])
        ->thenReturn();

    // Assert.
    expect($runs)->toBe([1, 2, 3]);
});

it('should throw when passing an invalid pipe', function () {
    // Arrange.
    $pipeline = new Pipeline();

    // Act & Assert.
    expect(function () use ($pipeline) {
        $pipeline
            ->send(1)
            ->through([
                'non-existing-class',
            ])
            ->then(function ($passable) {
                return $passable;
            });
    })->toThrow(InvalidPipe::class);
});
