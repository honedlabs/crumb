<?php

declare(strict_types=1);

use Honed\Crumb\Exceptions\TrailCannotTerminateException;

it('throws', function () {
    TrailCannotTerminateException::throw();
})->throws(TrailCannotTerminateException::class);
