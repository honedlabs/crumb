<?php

declare(strict_types=1);

use Honed\Crumb\Exceptions\TrailNotFoundException;

it('throws', function () {
    TrailNotFoundException::throw('test');
})->throws(TrailNotFoundException::class);
