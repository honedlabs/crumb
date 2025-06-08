<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Source File(s)
    |--------------------------------------------------------------------------
    |
    | Provide the location where any global crumbs are defined. These can
    | be used to define crumb trails which apply to multiple routes.
    |
    */

    'files' => base_path('routes/crumbs.php'),

    /*
    |--------------------------------------------------------------------------
    | Default crumb
    |--------------------------------------------------------------------------
    |
    | Provide a default crumb group to use for when no crumb is specified for
    | middleware.
    |
    */

    'default' => null,
];
