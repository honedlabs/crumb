<?php

declare(strict_types=1);

namespace Honed\Crumb\Exceptions;

use InvalidArgumentException;

use function sprintf;

class DuplicateTrailException extends InvalidArgumentException
{
    /**
     * Create a new duplicate trail exception.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct(
            sprintf(
                'There is already a trail named [%s].',
                $name
            )
        );
    }

    /**
     * Throw a new duplicate trail exception.
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
