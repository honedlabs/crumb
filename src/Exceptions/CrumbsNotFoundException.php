<?php

declare(strict_types=1);

namespace Honed\Crumb\Exceptions;

class CrumbsNotFoundException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct(
            \sprintf('There were no defined crumbs found for [%s]',
                $name)
        );
    }
}
