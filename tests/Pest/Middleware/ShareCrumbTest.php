<?php

declare(strict_types=1);

use Honed\Crumb\Support\Constants;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

it('shares crumb', function () {
    get('/')->assertInertia(fn (Assert $page) => $page
        ->has(Constants::Prop, fn (Assert $nav) => $nav
            ->has('primary', fn (Assert $primary) => $primary
                ->has(0, fn (Assert $item) => $item
                    ->where('label', 'Home')
                    ->where('url', url('/'))
                    ->where('icon', null)
                )
                ->has(1, fn (Assert $item) => $item
                    ->where('label', 'About')
                    ->where('url', url('/about'))
                    ->where('icon', null)
                )
                ->has(2, fn (Assert $item) => $item
                    ->where('label', 'Contact')
                    ->where('url', url('/contact'))
                    ->where('icon', null)
                )
            )
        )
    );
});
