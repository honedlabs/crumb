<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests\Fixtures;

use Honed\Crumb\Concerns\HasCrumbs;
use Illuminate\Routing\Controller;

class PropertyController extends Controller
{
    use HasCrumbs;

    public $crumb = 'basic';

    public function show(string $word)
    {
        return inertia('Word/Show', ['word' => $word]);
    }
}
