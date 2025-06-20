<?php

declare(strict_types=1);

use Honed\Crumb\TrailManager;

it('has a `crumbs` helper', function () {
    expect(crumbs())->toBeInstanceOf(TrailManager::class);
});
