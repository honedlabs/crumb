<?php

declare(strict_types=1);

beforeEach(function () {});

arch()->preset()->php();

arch()->preset()->security();

arch('debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict')
    ->expect('Honed\Crumb')
    ->toUseStrictTypes()
    ->toUseStrictEquality();

arch('documented')
    ->expect('Honed\Crumb')
    ->toHaveMethodsDocumented()
    ->toHavePropertiesDocumented();

arch('concerns')
    ->expect('Honed\Crumb\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Crumb\Contracts')
    ->toBeInterfaces();

arch('exceptions')
    ->expect('Honed\Crumb\Exceptions')
    ->toBeClasses()
    ->toExtend(Exception::class)
    ->toHaveSuffix('Exception');
