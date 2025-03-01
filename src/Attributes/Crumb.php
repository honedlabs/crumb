<?php

declare(strict_types=1);

namespace Honed\Crumb\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Crumb
{
    public function __construct(protected readonly string $crumb)
    {
        //
    }

    /**
     * Get the name of the crumb.
     *
     * @return string
     */
    public function getCrumbName()
    {
        return $this->crumb;
    }
}
