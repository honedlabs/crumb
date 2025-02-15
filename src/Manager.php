<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Crumb\Exceptions\CrumbsNotFoundException;
use Honed\Crumb\Exceptions\DuplicateCrumbsException;

class Manager
{
    const ShareProp = 'crumbs';

    /**
     * @var array<string,\Closure>
     */
    protected $trails = [];

    /**
     * @var \Closure|null
     */
    protected $before = null;

    /**
     * Set crumbs to be added globally, before all other crumbs.
     */
    public function before(\Closure $trail): void
    {
        $this->before = $trail;
    }

    /**
     * Set a crumb trail for a given name.
     */
    public function for(string $name, \Closure $trail): void
    {
        if ($this->exists($name)) {
            throw new DuplicateCrumbsException($name);
        }

        $this->trails[$name] = $trail;
    }

    /**
     * Determine if the crumb trail is defined.
     */
    public function exists(string $name): bool
    {
        return isset($this->trails[$name]);
    }

    /**
     * Retrieve a crumb trail by name.
     *
     * @throws \Honed\Crumb\Exceptions\CrumbsNotFoundException
     */
    public function get(string $name): Trail
    {
        if (! $this->exists($name)) {
            static::throwCrumbNotFoundException($name);
        }

        $trail = Trail::make()->terminating();

        if ($this->before) {
            ($this->before)($trail);
        }

        ($this->trails[$name])($trail);

        return $trail;
    }

    /**
     * Throw an exception for a missing crumb trail.
     */
    protected static function throwCrumbNotFoundException(string $crumb): never
    {
        throw new CrumbsNotFoundException($crumb);
    }
}
