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
     * The trails defined for the application.
     *
     * @var array<string,Closure>
     */
    protected $trails = [];

    /**
     * The trail to be added globally, before all other crumbs.
     *
     * @var Closure|null
     */
    protected $before = null;

    /**
     * Set a crumb to be added globally, before all other crumbs.
     *
     * @return $this
     */
    public function before(Closure $trail): static
    {
        $this->before = $trail;

        return $this;
    }

    /**
     * Set a crumb trail for a given name.
     *
     * @return $this
     *
     * @throws DuplicateTrailException
     */
    public function for(string $name, Closure $trail): static
    {
        if ($this->hasTrail($name)) {
            DuplicateTrailException::throw($name);
        }

        Arr::set($this->trails, $name, $trail);

        return $this;
    }

    /**
     * Determine if the trail exists.
     */
    public function hasTrail(string $name): bool
    {
        return in_array($name, array_keys($this->trails));
    }

    /**
     * Retrieve a crumb trail by name.
     *
     *
     * @throws TrailNotFoundException
     */
    public function get(string $name): Trail
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
