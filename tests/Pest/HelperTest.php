<?php

use Honed\Crumb\TrailManager;
use Honed\Crumb\Facades\Crumbs;

it('has a `crumbs` helper', function () {
    expect(crumbs())->toBeInstanceOf(TrailManager::class);
});
