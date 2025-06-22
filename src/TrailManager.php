<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Closure;
use Honed\Crumb\Exceptions\DuplicateTrailException;
use Honed\Crumb\Exceptions\TrailNotFoundException;
use Illuminate\Support\Arr;

use function array_keys;
use function call_user_func;
use function in_array;

class TrailManager
{
    /**
     * @var array<string,Closure>
     */
    protected $trails = [];

    /**
     * @var Closure|null
     */
    protected $before = null;

    /**
     * Set a crumb to be added globally, before all other crumbs.
     *
     * @param  Closure  $trail
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
     * @param  Closure  $trail
     * @return $this
     *
     * @throws DuplicateTrailException
     */
    public function for($name, $trail)
    {
        if ($this->hasTrail($name)) {
            DuplicateTrailException::throw($name);
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
        return in_array($name, array_keys($this->trails));
    }

    /**
     * Retrieve a crumb trail by name.
     *
     * @param  string  $name
     * @return Trail
     *
     * @throws TrailNotFoundException
     */
    public function get($name)
    {
        if (! $this->hasTrail($name)) {
            TrailNotFoundException::throw($name);
        }

        $trail = Trail::make()->terminating();

        if ($this->before) {
            call_user_func($this->before, $trail);
        }

        /** @var Closure */
        $callback = Arr::get($this->trails, $name);

        call_user_func($callback, $trail);

        return $trail;
    }
}
