<?php

namespace Cms;

use Closure;
use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, Closure $factory): void
    {
        $this->bind($abstract, $factory);
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    public function make(string $abstract): mixed
    {
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $object = ($this->bindings[$abstract])($this);
            $this->instances[$abstract] = $object;

            return $object;
        }

        if (class_exists($abstract)) {
            return $this->instances[$abstract] = $this->build($abstract);
        }

        throw new RuntimeException("No binding found for [{$abstract}]");
    }

    /**
     * Instantiate a concrete class, autowiring constructor dependencies
     * by their type-hints (recursively resolved through the container).
     */
    private function build(string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $args = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->make($type->getName());

                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();

                continue;
            }

            throw new RuntimeException(
                "Cannot resolve parameter [{$parameter->getName()}] for class [{$class}]"
            );
        }

        return $reflection->newInstanceArgs($args);
    }
}
