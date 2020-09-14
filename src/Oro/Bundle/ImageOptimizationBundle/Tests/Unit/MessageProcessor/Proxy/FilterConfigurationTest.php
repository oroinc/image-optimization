<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\MessageProcessor\Proxy;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\HashProvider;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;
use Oro\Bundle\ImageOptimizationBundle\MessageProcessor\Proxy\FilterConfiguration as ProxyFilterConfiguration;

class FilterConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /** @var FilterConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private $filterConfiguration;

    /** @var HashProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $processorProvider;

    /** @var ProxyFilterConfiguration */
    private $proxyFilterConfiguration;

    protected function setUp(): void
    {
        $this->filterConfiguration = $this->createMock(FilterConfiguration::class);
        $this->processorProvider = $this->createMock(ProcessorsProvider::class);

        $this->proxyFilterConfiguration = new ProxyFilterConfiguration(
            $this->filterConfiguration,
            $this->processorProvider
        );
    }

    public function testGet(): void
    {
        $filter = ['filter1' => ['option1' => 'value1']];
        $this->processorProvider
            ->expects($this->once())
            ->method('isPostProcessingEnabled')
            ->willReturn(false);

        $this->filterConfiguration
            ->expects($this->once())
            ->method('getOriginal')
            ->willReturn($filter);

        $this->filterConfiguration
            ->expects($this->never())
            ->method('get');

        $this->assertEquals($filter, $this->proxyFilterConfiguration->get('filterName'));
    }

    public function testGetOriginal(): void
    {
        $filter = ['filter1' => ['option1' => 'value1']];
        $this->processorProvider
            ->expects($this->once())
            ->method('isPostProcessingEnabled')
            ->willReturn(true);

        $this->filterConfiguration
            ->expects($this->once())
            ->method('get')
            ->willReturn($filter);

        $this->filterConfiguration
            ->expects($this->never())
            ->method('getOriginal');

        $this->assertEquals($filter, $this->proxyFilterConfiguration->get('filterName'));
    }

    public function testSet(): void
    {
        $this->processorProvider
            ->expects($this->never())
            ->method($this->anything());

        $this->filterConfiguration
            ->expects($this->once())
            ->method('set')
            ->with('filterName', ['option1' => 'value1']);

        $this->proxyFilterConfiguration->set('filterName', ['option1' => 'value1']);
    }

    public function testAll(): void
    {
        $filter = ['filter1' => ['option1' => 'value1']];
        $this->processorProvider
            ->expects($this->never())
            ->method($this->anything());

        $this->filterConfiguration
            ->expects($this->once())
            ->method('all')
            ->willReturn([$filter]);

        $this->assertEquals([$filter], $this->proxyFilterConfiguration->all());
    }
}
