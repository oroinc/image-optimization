<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\Configurator;

use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;

class FilterConfigurationTest extends \PHPUnit\Framework\TestCase
{
    private const FILTER_NAME = 'filter_name';

    /** @var FilterConfiguration */
    private $filterConfiguration;

    /** @var FilterConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private $filterConfigurationInner;

    /** @var ProcessorsProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $processorsProvider;

    protected function setUp(): void
    {
        $this->filterConfigurationInner = $this->createMock(FilterConfiguration::class);
        $this->processorsProvider = $this->createMock(ProcessorsProvider::class);
        $this->filterConfiguration = new FilterConfiguration(
            $this->filterConfigurationInner,
            $this->processorsProvider
        );
    }

    /**
     * @dataProvider filterProvider
     *
     * @param array $actual
     * @param array $expected
     */
    public function testGet(array $actual, array $expected): void
    {
        $this->filterConfigurationInner
            ->expects($this->once())
            ->method('get')
            ->with(self::FILTER_NAME)
            ->willReturn($actual);

        $this->processorsProvider
            ->expects($this->any())
            ->method('getFilterConfig')
            ->willReturn($expected['post_processors']);

        $this->assertEquals($expected, $this->filterConfiguration->get(self::FILTER_NAME));
    }

    public function testSet(): void
    {
        $filter = [
            'filter_option' => ['option'],
            'post_processors' => [
                'processor1' => ['processor_option1' => 'option1'],
                'processor2' => ['processor_option2' => 'option2']
            ]
        ];
        $this->filterConfigurationInner
            ->expects($this->once())
            ->method('set')
            ->with(self::FILTER_NAME, $filter);

        $this->filterConfiguration->set(self::FILTER_NAME, $filter);
    }

    /**
     * @dataProvider filterProvider
     *
     * @param array $actual
     * @param array $expected
     */
    public function testAll(array $actual, array $expected): void
    {
        $this->filterConfigurationInner
            ->expects($this->exactly(1))
            ->method('all')
            ->willReturn([$actual]);

        $this->processorsProvider
            ->expects($this->any())
            ->method('getFilterConfig')
            ->willReturn($expected['post_processors']);

        $this->assertEquals([$expected], $this->filterConfiguration->all());
    }

    /**
     * @return array
     */
    public function filterProvider(): array
    {
        return [
            'With post processors' => [
                'actual' => [
                    'filter_option' => ['option'],
                    'post_processors' => [
                        'processor1' => ['processor_option' => 'option']
                    ],
                ],
                'expected' => [
                    'filter_option' => ['option'],
                    'post_processors' => [
                        'processor1' => ['processor_option' => 'option']
                    ]
                ]
            ],
            'With empty post processors' => [
                'actual' => [
                    'filter_option' => ['option'],
                    'post_processors' => []
                ],
                'expected' => [
                    'filter_option' => ['option'],
                    'post_processors' => [
                        'processor1' => ['processor_option1' => 'option1'],
                        'processor2' => ['processor_option2' => 'option2']
                    ]
                ]
            ],
            'Without post processors' => [
                'actual' => [
                    'filter_option' => ['option'],
                ],
                'expected' => [
                    'filter_option' => ['option'],
                    'post_processors' => [
                        'processor1' => ['processor_option1' => 'option1'],
                        'processor2' => ['processor_option2' => 'option2']
                    ]
                ]
            ]
        ];
    }
}
