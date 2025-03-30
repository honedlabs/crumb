<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Support\Arr;

class CrumbFactory
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
     * @param  \Closure  $trail
     * @return $this
     */
    public function before($trail)
    {
        $this->before = $trail;

        return $this;
    }

    /**
     * Set a crumb trail for a given name.
     *
     * @param  string  $name
     * @param  \Closure  $trail
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function for($name, $trail)
    {
        if ($this->hasTrail($name)) {
            static::throwDuplicateCrumbsException($name);
        }

        Arr::set($this->trails, $name, $trail);

        return $this;
    }

    /**
     * Determine if the trail exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasTrail($name)
    {
        return \in_array($name, \array_keys($this->trails));
    }

    /**
     * Retrieve a crumb trail by name.
     *
     * @param  string  $name
     * @return \Honed\Crumb\Trail
     *
     * @throws \InvalidArgumentException
     */
    public function get($name)
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
     *
     * @param  string  $crumb
     * @return never
     *
     * @throws \InvalidArgumentException
     */
    public static function throwCrumbNotFoundException($crumb)
    {
        throw new \InvalidArgumentException(
            \sprintf('There were no crumbs defined for [%s].', $crumb)
        );
    }

    /**
     * Throw an exception for a duplicate crumb trail.
     *
     * @param  string  $crumb
     * @return never
     *
     * @throws \InvalidArgumentException
     */
    public static function throwDuplicateCrumbsException($crumb)
    {
        throw new \InvalidArgumentException(
            \sprintf('There already exists a crumb with the name [%s].', $crumb)
        );
    }
}
