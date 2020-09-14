<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Functional\Configurator;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolationPerTest
 */
class FilterConfigurationTest extends WebTestCase
{
    use SettingsTrait;

    /** @var FilterConfiguration */
    private $filterConfigurator;

    protected function setUp(): void
    {
        $this->initClient([], $this->generateWsseAuthHeader());
        $this->filterConfigurator =
            $this->getContainer()->get('oro_image_optimization.configurator.filter_configuration');
    }

    public function testWithLibrariesExistsAndConfigNotChanged(): void
    {
        // Libraries exists but quality parameters was not changed
        // Always accept the configuration(post_processor => [...]) for the filter if libraries exist
        $filters = $this->filterConfigurator->all();
        foreach ($filters as $filterName => $filterConfig) {
            $specificFilterConfig = $this->filterConfigurator->get($filterName);
            $this->assertFilterEqual($filterConfig, 85, 100);
            $this->assertFilterEqual($specificFilterConfig, 85, 100);
        }
    }

    public function testWithLibrariesExistsAndConfigChanged(): void
    {
        $this->changeQualityParameters(65, 35);
        $filters = $this->filterConfigurator->all();
        foreach ($filters as $filterName => $filterConfig) {
            $specificFilterConfig = $this->filterConfigurator->get($filterName);
            $this->assertFilterEqual($filterConfig, 65, 35);
            $this->assertFilterEqual($specificFilterConfig, 65, 35);
        }
    }

    /**
     * @param array $filter
     * @param int $jpegQuality
     * @param int $pngQuality
     */
    private function assertFilterEqual(array $filter, int $jpegQuality = 85, int $pngQuality = 100)
    {
        $this->assertEquals(
            $filter['post_processors'],
            [
                'pngquant' => ['quality' => $pngQuality],
                'jpegoptim' => ['strip_all' => true, 'max' => $jpegQuality, 'progressive' => false],
            ]
        );
    }
}
