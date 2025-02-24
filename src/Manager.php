<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Support\Arr;

class Manager
{
    /**
     * @var array<string,\Closure>
     */
    protected $trails = [];

    /**
     * @var \Closure|null
     */
    protected $before = null;

    /**
     * Set a crumb to be added globally, before all other crumbs.
     *
     * @return $this
     */
    public function before(\Closure $trail): static
    {
        $this->before = $trail;

        return $this;
    }

    /**
     * Set a crumb trail for a given name.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function for(string $name, \Closure $trail): static
    {
        if ($this->hasTrail($name)) {
            static::throwDuplicateCrumbsException($name);
        }

        Arr::set($this->trails, $name, $trail);

        return $this;
    }

    /**
     * Determine if the trail exists.
     */
    public function hasTrail(string $name): bool
    {
        return Arr::has($this->trails, $name);
    }

    /**
     * Retrieve a crumb trail by name.
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $name): Trail
    {
        if (! $this->hasTrail($name)) {
            static::throwCrumbNotFoundException($name);
        }

        $trail = Trail::make()->terminating();

        if ($this->before) {
            \call_user_func($this->before, $trail);
        }

        /** @var \Closure */
        $callback = Arr::get($this->trails, $name);

        \call_user_func($callback, $trail);

        return $trail;
    }

    /**
     * Throw an exception for a missing crumb trail.
     */
    protected static function throwCrumbNotFoundException(string $crumb): never
    {
        throw new \InvalidArgumentException(
            \sprintf('There were no crumbs defined for [%s].',
                $crumb
            ));
    }

    /**
     * Throw an exception for a duplicate crumb trail.
     */
    protected static function throwDuplicateCrumbsException(string $crumb): never
    {
        throw new \InvalidArgumentException(
            \sprintf('There already exists a crumb with the name [%s].',
                $crumb
            ));
    }
}
