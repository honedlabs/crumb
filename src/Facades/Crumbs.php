<?php

namespace Honed\Crumb\Facades;

use Honed\Crumb\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Honed\Crumb\Manager
 */
class Crumbs extends Facade
{
    const ShareProp = Manager::ShareProp;

    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
