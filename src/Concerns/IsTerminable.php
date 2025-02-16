<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

trait IsTerminable
{
    /**
     * @var bool
     */
    protected $terminating = false;

    /**
     * @var bool
     */
    protected $terminated = false;

    /**
     * Set the trail to terminate when a crumb in the trail matches.
     *
     * @return $this
     */
    public function terminating(bool $terminating = true): static
    {
        $this->terminating = $terminating;

        return $this;
    }

    /**
     * Determine if the trail is terminating.
     */
    public function isTerminating(): bool
    {
        return $this->terminating;
    }

    /**
     * Determine if the trail has terminated.
     */
    public function hasTerminated(): bool
    {
        return $this->terminated;
    }

    /**
     * Set the trail to have terminated.
     *
     * @return $this
     */
    protected function terminate(bool $terminated = true): static
    {
        $this->terminated = $terminated;

        return $this;
    }
}
