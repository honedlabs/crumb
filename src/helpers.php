<?php

use Honed\Crumb\Facades\Crumbs;

if (! \function_exists('crumbs')) {
    /**
     * Access the crumb factory.
     *
     * @return \Honed\Crumb\CrumbFactory
     */
    function crumbs()
    {
        $instance = Crumbs::getFacadeRoot();

        return $instance;
    }
}
