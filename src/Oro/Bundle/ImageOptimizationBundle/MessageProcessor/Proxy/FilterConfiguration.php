<?php

namespace Oro\Bundle\ImageOptimizationBundle\MessageProcessor\Proxy;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration as BaseFilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;

/**
 * Proxy class for resolve backward compatibility.
 * Used if the dependent service built a hash based on filters.
 */
class FilterConfiguration extends BaseFilterConfiguration
{
    /**
     * @var BaseFilterConfiguration
     */
    private $filterConfigurator;

    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    /**
     * @param BaseFilterConfiguration $filterConfigurator
     * @param ProcessorsProvider $processorsProvider
     */
    public function __construct(BaseFilterConfiguration $filterConfigurator, ProcessorsProvider $processorsProvider)
    {
        $this->filterConfigurator = $filterConfigurator;
        $this->processorsProvider = $processorsProvider;
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function get($filter): array
    {
        return $this->processorsProvider->isPostProcessingEnabled()
            ? $this->filterConfigurator->get($filter)
            : $this->filterConfigurator->getOriginal($filter);
    }

    /**
     * @param string $filter
     * @param array $config
     */
    public function set($filter, array $config): void
    {
        $this->filterConfigurator->set($filter, $config);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->filterConfigurator->all();
    }
}
