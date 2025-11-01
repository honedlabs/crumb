<?php

declare(strict_types=1);

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Crumb')
    ->toUseStrictTypes();

arch('attributes')
    ->expect('Honed\Crumb\Attributes')
    ->toBeClasses();

arch('concerns')
    ->expect('Honed\Crumb\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Crumb\Contracts')
    ->toBeInterfaces();

arch('facades')
    ->expect('Honed\Crumb\Facades')
    ->toExtend('Illuminate\Support\Facades\Facade');
