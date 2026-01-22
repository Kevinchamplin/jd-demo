<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\Container;

use Closure;
use InvalidArgumentException;
use RuntimeException;

/**
 * A lightweight Dependency Injection Container
 *
 * Provides PSR-11 inspired container functionality for managing
 * service dependencies throughout the plugin lifecycle.
 *
 * @package Xebia\AuthorityEngine\Container
 */
final class Container
{
    /**
     * Registered service bindings
     *
     * @var array<string, Closure>
     */
    private array $bindings = [];

    /**
     * Singleton instances cache
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Services marked as singletons
     *
     * @var array<string, bool>
     */
    private array $singletons = [];

    /**
     * Services currently being resolved (for circular dependency detection)
     *
     * @var array<string, bool>
     */
    private array $resolving = [];

    /**
     * Register a binding in the container
     *
     * @param string  $abstract The service identifier
     * @param Closure $concrete The factory closure
     */
    public function bind(string $abstract, Closure $concrete): self
    {
        $this->bindings[$abstract] = $concrete;
        unset($this->instances[$abstract], $this->singletons[$abstract]);

        return $this;
    }

    /**
     * Register a singleton binding in the container
     *
     * @param string  $abstract The service identifier
     * @param Closure $concrete The factory closure
     */
    public function singleton(string $abstract, Closure $concrete): self
    {
        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = true;
        unset($this->instances[$abstract]);

        return $this;
    }

    /**
     * Register an existing instance as a singleton
     *
     * @param string $abstract The service identifier
     * @param object $instance The instance to register
     */
    public function instance(string $abstract, object $instance): self
    {
        $this->instances[$abstract] = $instance;
        $this->singletons[$abstract] = true;

        return $this;
    }

    /**
     * Resolve a service from the container
     *
     * @template T
     * @param class-string<T> $abstract The service identifier
     * @return T The resolved service instance
     * @throws RuntimeException If the service cannot be resolved
     */
    public function get(string $abstract): object
    {
        // Return cached singleton instance if available
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Check if binding exists
        if (!isset($this->bindings[$abstract])) {
            throw new RuntimeException(
                sprintf('Service [%s] is not registered in the container.', $abstract)
            );
        }

        // Detect circular dependencies
        if (isset($this->resolving[$abstract])) {
            throw new RuntimeException(
                sprintf('Circular dependency detected while resolving [%s].', $abstract)
            );
        }

        $this->resolving[$abstract] = true;

        try {
            $concrete = $this->bindings[$abstract];
            $instance = $concrete($this);

            // Cache singleton instances
            if (isset($this->singletons[$abstract])) {
                $this->instances[$abstract] = $instance;
            }

            return $instance;
        } finally {
            unset($this->resolving[$abstract]);
        }
    }

    /**
     * Check if a service is registered
     *
     * @param string $abstract The service identifier
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Remove a service from the container
     *
     * @param string $abstract The service identifier
     */
    public function forget(string $abstract): self
    {
        unset(
            $this->bindings[$abstract],
            $this->instances[$abstract],
            $this->singletons[$abstract]
        );

        return $this;
    }

    /**
     * Clear all bindings and instances
     */
    public function flush(): self
    {
        $this->bindings = [];
        $this->instances = [];
        $this->singletons = [];
        $this->resolving = [];

        return $this;
    }

    /**
     * Get all registered service identifiers
     *
     * @return array<string>
     */
    public function getRegisteredServices(): array
    {
        return array_unique([
            ...array_keys($this->bindings),
            ...array_keys($this->instances),
        ]);
    }
}
