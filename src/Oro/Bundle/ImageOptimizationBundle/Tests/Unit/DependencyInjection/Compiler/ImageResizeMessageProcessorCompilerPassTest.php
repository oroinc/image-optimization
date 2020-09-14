<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection;

use Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Compiler\ImageResizeMessageProcessorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ImageResizeMessageProcessorCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    /** @var ImageResizeMessageProcessorCompilerPass */
    private $compiler;

    protected function setUp()
    {
        $this->compiler = new ImageResizeMessageProcessorCompilerPass();
    }

    public function testProcessWithService(): void
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('oro_multiwebsite.message_processor.image_resize')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('addDefinitions');
        $reference = new Reference('oro_image_optimization.message_processor.proxy.filter_configuration');
        $definition = $this->createMock(Definition::class);
        $definition
            ->expects($this->once())
            ->method('replaceArgument')
            ->with(5, $reference);
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('oro_multiwebsite.message_processor.image_resize')
            ->willReturn($definition);

        $this->compiler->process($container);
    }

    public function testProcessWithoutService(): void
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('oro_multiwebsite.message_processor.image_resize')
            ->willReturn(false);

        $this->compiler->process($container);
    }
}
