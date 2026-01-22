<?php

declare(strict_types=1);

use Xebia\AuthorityEngine\Container\Container;

describe('Container', function () {
    beforeEach(function () {
        $this->container = new Container();
    });

    it('registers and resolves a binding', function () {
        $this->container->bind('test', fn() => new stdClass());

        $result = $this->container->get('test');

        expect($result)->toBeInstanceOf(stdClass::class);
    });

    it('registers singleton bindings', function () {
        $this->container->singleton('test', fn() => new stdClass());

        $first = $this->container->get('test');
        $second = $this->container->get('test');

        expect($first)->toBe($second);
    });

    it('creates new instances for non-singleton bindings', function () {
        $this->container->bind('test', fn() => new stdClass());

        $first = $this->container->get('test');
        $second = $this->container->get('test');

        expect($first)->not->toBe($second);
    });

    it('detects circular dependencies', function () {
        $this->container->singleton('a', fn(Container $c) => $c->get('b'));
        $this->container->singleton('b', fn(Container $c) => $c->get('a'));

        expect(fn() => $this->container->get('a'))
            ->toThrow(RuntimeException::class, 'Circular dependency');
    });

    it('throws exception for unregistered services', function () {
        expect(fn() => $this->container->get('nonexistent'))
            ->toThrow(RuntimeException::class, 'not registered');
    });

    it('checks if service exists', function () {
        $this->container->bind('exists', fn() => true);

        expect($this->container->has('exists'))->toBeTrue();
        expect($this->container->has('notexists'))->toBeFalse();
    });

    it('can forget a service', function () {
        $this->container->bind('forgettable', fn() => true);
        $this->container->forget('forgettable');

        expect($this->container->has('forgettable'))->toBeFalse();
    });

    it('can flush all services', function () {
        $this->container->bind('a', fn() => 'a');
        $this->container->bind('b', fn() => 'b');
        $this->container->flush();

        expect($this->container->getRegisteredServices())->toBe([]);
    });
});
