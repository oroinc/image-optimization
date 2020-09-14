<?php

namespace Oro\Bundle\ImageOptimizationBundle\Configurator\Provider;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;

/**
 * Responsible for build unique hash and involved in building the url hash and specifying the location of the
 * attachment storage.
 */
class HashProvider
{
    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    /**
     * @var FilterConfiguration
     */
    private $filterConfiguration;

    /**
     * @param ProcessorsProvider $processorsProvider
     * @param FilterConfiguration $filterConfiguration
     */
    public function __construct(ProcessorsProvider $processorsProvider, FilterConfiguration $filterConfiguration)
    {
        $this->processorsProvider = $processorsProvider;
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * @param string $filterName
     *
     * @return string
     */
    public function getFilterConfigHash(string $filterName): string
    {
        $filterConfig = $this->processorsProvider->isPostProcessingEnabled()
            ? $this->filterConfiguration->get($filterName)
            : $this->filterConfiguration->getOriginal($filterName);

        return md5(json_encode($filterConfig));
    }
}
