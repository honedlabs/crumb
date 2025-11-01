<?php

declare(strict_types=1);

use Honed\Crumb\Concerns\HasCrumbs;
use Illuminate\Routing\Controller;

it('must be controller', function () {
    $test = new class {
        use HasCrumbs;
    };
})->throws(\LogicException::class);

it('gets crumb from method', function () {
    $test = new class extends Controller {
        use HasCrumbs;

        public function crumb()
        {
            return 'test';
        }
    };

    expect($test->getCrumbName())->toBe('test');
});
