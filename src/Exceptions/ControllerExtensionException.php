<?php

declare(strict_types=1);

namespace Honed\Crumb\Exceptions;

class ControllerExtensionException extends \LogicException
{
    /**
     * Create a new controller extension exception.
     *
     * @param  class-string  $class
     */
    public function __construct($class)
    {
        parent::__construct(
            \sprintf(
                'Class [%s] does not extend the [Illuminate\Routing\Controller] controller class.',
                $class
            )
        );
    }

    /**
     * Throw a new controller extension exception.
     *
     * @param  class-string  $class
     * @return never
     *
     * @throws static
     */
    public static function throw($class)
    {
        throw new self($class);
    }
}
