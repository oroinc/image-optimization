<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection;

use Oro\Component\Config\CumulativeResourceManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroImageOptimizationExtensionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $configuration;

    public function testLoadParameters(): void
    {
        CumulativeResourceManager::getInstance()->clear();

        $configs = [];
        $extension = new OroImageOptimizationExtension();
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->exactly(4))
            ->method('setParameter')
            ->withConsecutive(
                ['liip_imagine.jpegoptim.binary', null],
                ['liip_imagine.pngquant.binary', null],
                ['oro_image_optimization.png_quality', 100],
                ['oro_image_optimization.jpeg_quality', 85],
            );

        $extension->load($configs, $container);
    }
}
