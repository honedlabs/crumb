<?php

declare(strict_types=1);

namespace Honed\Crumb\Exceptions;

use InvalidArgumentException;

use function sprintf;

class TrailNotFoundException extends InvalidArgumentException
{
    /**
     * Create a new trail not found exception.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct(
            sprintf(
                'No trail named [%s] exists.',
                $name
            )
        );
    }

    /**
     * Throw a new trail not found exception.
     *
     * @param  string  $name
     * @return never
     *
     * @throws static
     */
    public static function throw($name)
    {
        throw new self($name);
    }
}
