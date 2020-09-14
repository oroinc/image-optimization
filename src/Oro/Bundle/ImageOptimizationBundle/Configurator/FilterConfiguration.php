<?php

namespace Oro\Bundle\ImageOptimizationBundle\Configurator;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration as BaseFilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;

/**
 * Image filter configurator. Adds a "post-processor" configuration to the filter config
 * (liip_imagine or theme dimensions).
 */
class FilterConfiguration extends BaseFilterConfiguration
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
     * @param BaseFilterConfiguration $filterConfiguration
     * @param ProcessorsProvider $processorsProvider
     */
    public function __construct(
        BaseFilterConfiguration $filterConfiguration,
        ProcessorsProvider $processorsProvider
    ) {
        $this->filterConfiguration = $filterConfiguration;
        $this->processorsProvider = $processorsProvider;
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function get($filter): array
    {
        $config = $this->filterConfiguration->get($filter);

        return $this->addProcessorsConfig($config);
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function getOriginal($filter): array
    {
        return $this->filterConfiguration->get($filter);
    }

    /**
     * @param string $filter
     * @param array $config
     */
    public function set($filter, array $config): void
    {
        $this->filterConfiguration->set($filter, $config);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return array_map(
            function (array $config) {
                return $this->addProcessorsConfig($config);
            },
            $this->filterConfiguration->all()
        );
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function addProcessorsConfig(array $config = []): array
    {
        // Default processors configuration takes precedence over system settings.
        if (empty($config['post_processors'])) {
            $config['post_processors'] = $this->processorsProvider->getFilterConfig();
        }

        return $config;
    }
}
