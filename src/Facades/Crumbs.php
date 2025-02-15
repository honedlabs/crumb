<?php

declare(strict_types=1);

namespace Honed\Crumb\Facades;

use Honed\Crumb\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Honed\Crumb\Manager
 */
class Crumbs extends Facade
{    
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
