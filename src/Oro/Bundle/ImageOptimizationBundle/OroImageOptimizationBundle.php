<?php

namespace Oro\Bundle\ImageOptimizationBundle;

use Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Compiler\ImageResizeMessageProcessorCompilerPass;
use Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Compiler\ProcessorsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The OroImageOptimizationBundle bundle class.
 */
class OroImageOptimizationBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ProcessorsCompilerPass());
        $container->addCompilerPass(new ImageResizeMessageProcessorCompilerPass());
    }
}
