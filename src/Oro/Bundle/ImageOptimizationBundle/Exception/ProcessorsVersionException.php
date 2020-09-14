<?php

namespace Oro\Bundle\ImageOptimizationBundle\Exception;

/**
 * Exception for invalid processors version.
 */
class ProcessorsVersionException extends \RuntimeException
{
    /**
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name, string $version)
    {
        $message = sprintf('The %s library version "%s" does not meet the needs of the system.', $name, $version);

        parent::__construct($message);
    }
}
