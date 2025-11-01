<?php

declare(strict_types=1);

use Honed\Crumb\Exceptions\DuplicateTrailException;

it('throws', function () {
    DuplicateTrailException::throw('test');
})->throws(DuplicateTrailException::class);
