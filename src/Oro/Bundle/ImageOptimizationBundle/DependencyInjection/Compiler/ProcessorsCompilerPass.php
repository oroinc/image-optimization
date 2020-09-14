<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Compiler;

use Oro\Bundle\ImageOptimizationBundle\ProcessorHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Update parameters if libraries exists.
 */
class ProcessorsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $processorHelper = new ProcessorHelper($container->getParameterBag());
        $librariesExists = false;
        try {
            $librariesExists = $processorHelper->librariesExists();
        } catch (\Exception $exception) {
            // Any error in catch indicates that the library does not exist or its version does not meet the
            // needs of the system
        }

        if ($librariesExists) {
            $container->setParameter('liip_imagine.pngquant.binary', $processorHelper->getPNGQuantLibrary());
            $container->setParameter('liip_imagine.jpegoptim.binary', $processorHelper->getJPEGOptimLibrary());
        }
    }
}
