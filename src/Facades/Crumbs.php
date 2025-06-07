<?php

declare(strict_types=1);

namespace Honed\Crumb\Facades;

use Honed\Crumb\TrailManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static static before(\Closure $trail) Set a crumb to be added globally
 * @method static static for(string $name, \Closure $trail) Set a crumb trail for a given name
 * @method static bool hasTrail(string $name) Determine if the tail exists
 * @method static \Honed\Crumb\Trail get(string $name) Retrieve a crumb trail by name
 *
 * @see TrailManager
 */
class Crumbs extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return TrailManager
     */
    public static function getFacadeRoot()
    {
        // @phpstan-ignore-next-line
        return parent::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return TrailManager::class;
    }
}
