<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\Configurator\Provider;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\HashProvider;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;

class HashProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var ProcessorsProvider|\PHPUnit\Framework\MockObject\MockObject  */
    private $processorsProvider;

    /** @var FilterConfiguration|\PHPUnit\Framework\MockObject\MockObject  */
    private $filterConfiguration;

    /** @var HashProvider */
    private $hashProvider;

    protected function setUp(): void
    {
        $this->processorsProvider = $this->createMock(ProcessorsProvider::class);
        $this->filterConfiguration = $this->createMock(FilterConfiguration::class);

        $this->hashProvider = new HashProvider(
            $this->processorsProvider,
            $this->filterConfiguration
        );
    }

    public function testGetUrlFilterConfigWithDefaultSystemConfiguration(): void
    {
        $filterName = 'filterName';
        $this->processorsProvider
            ->expects($this->once())
            ->method('isPostProcessingEnabled')
            ->willReturn(false);

        $this->filterConfiguration
            ->expects($this->once())
            ->method('getOriginal')
            ->with($filterName);

        $this->filterConfiguration
            ->expects($this->never())
            ->method('get');

        $this->hashProvider->getFilterConfigHash($filterName);
    }

    public function testGetUrlFilterConfigWithChangedSystemConfiguration(): void
    {
        $filterName = 'filterName';
        $this->processorsProvider
            ->expects($this->once())
            ->method('isPostProcessingEnabled')
            ->willReturn(true);

        $this->filterConfiguration
            ->expects($this->never())
            ->method('getOriginal');

        $this->filterConfiguration
            ->expects($this->once())
            ->method('get')
            ->with($filterName);

        $this->hashProvider->getFilterConfigHash($filterName);
    }
}
