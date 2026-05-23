<?php

declare(strict_types=1);

use Honed\Crumb\Crumb;
use Honed\Crumb\Exceptions\ControllerExtensionException;

it('throws', function () {
    ControllerExtensionException::throw(Crumb::class);
})->throws(ControllerExtensionException::class);
