<?php

declare(strict_types=1);

namespace Honed\Crumb\Exceptions;

class TrailCannotTerminateException extends \BadMethodCallException
{
    /**
     * Create a new trail cannot terminate exception.
     */
    public function __construct()
    {
        parent::__construct(
            'A non-terminating crumb cannot be terminated.'
        );
    }

    /**
     * Throw a new trail not found exception.
     *
     * @return never
     *
     * @throws static
     */
    public static function throw()
    {
        throw new self;
    }
}
