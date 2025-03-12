<?php

use Honed\Crumb\CrumbFactory;
use Honed\Crumb\Facades\Crumbs;

it('has a `crumbs` helper', function () {
    expect(crumbs())->toBeInstanceOf(CrumbFactory::class);
});
