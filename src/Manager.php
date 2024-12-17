<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Crumb\Exceptions\CrumbsNotFoundException;
use Honed\Crumb\Exceptions\DuplicateCrumbsException;

class Manager
{
    /**
     * Trails paired to a key.
     *
     * @var array<string,(\Closure(\Honed\Crumb\Trail):void)>
     */
    protected $trails = [];

    /**
     * Crumbs to be added before the trail.
     * Useful for adding a home crumb to all trails.
     *
     * @var (\Closure(\Honed\Crumb\Trail):void)|null
     */
    protected $before = null;

    /**
     * Set crumbs to be added globally, before all other crumbs.
     *
     * @param  (\Closure(\Honed\Crumb\Trail $trail):void)  $trail
     */
    public function before(\Closure $trail): void
    {
        $this->before = $trail;
    }

    /**
     * Set a crumb trail for a given name.
     *
     * @param  (\Closure(\Honed\Crumb\Trail $trail):void)  $trail
     */
    public function for(string $name, \Closure $trail): void
    {
        if ($this->exists($name)) {
            throw new DuplicateCrumbsException($name);
        }

        $this->trails[$name] = $trail;
    }

    /**
     * Determine if a crumb with the given name exists.
     */
    public function exists(string $name): bool
    {
        return isset($this->trails[$name]);
    }

    /**
     * Retrieve a crumb trail by name.
     *
     *
     * @throws \Honed\Crumb\Exceptions\CrumbsNotFoundException
     */
    public function get(string $name): Trail
    {
        if (! $this->exists($name)) {
            throw new CrumbsNotFoundException($name);
        }

        $trail = Trail::make()->locking();

        if ($this->before) {
            ($this->before)($trail);
        }

        ($this->trails[$name])($trail);

        return $trail;
    }
}
