<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();

        $treeBuilder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }

    public function testProcessConfiguration()
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $expected = [
            'settings' => [
                'png_quality' => [
                    'value' => 100,
                    'scope' => 'app'
                ],
                'jpeg_quality' => [
                    'value' => 85,
                    'scope' => 'app'
                ],
                'resolved' => true
            ],
            'png_quality' => 100,
            'jpeg_quality' => 85,
        ];

        $this->assertEquals($expected, $processor->processConfiguration($configuration, []));
    }
}
