<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Compiler;

use Oro\Bundle\ImageOptimizationBundle\MessageProcessor\Proxy\FilterConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces one of the dependencies with a proxy service. This ensures that the old service creates the correct caches.
 */
class ImageResizeMessageProcessorCompilerPass implements CompilerPassInterface
{
    private const MESSAGE_PROCESSOR = 'oro_multiwebsite.message_processor.image_resize';
    private const PROCESSOR_PROVIDER = 'oro_image_optimization.configurator.provider.processors_provider';
    private const FILTER_CONFIGURATION = 'oro_image_optimization.configurator.filter_configuration';
    private const PROXY_FILTER_CONFIGURATOR = 'oro_image_optimization.message_processor.proxy.filter_configuration';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::MESSAGE_PROCESSOR)) {
            return;
        }

        $proxyFilterConfigurationDefinition = new Definition(FilterConfiguration::class);
        $proxyFilterConfigurationDefinition->setArguments([
            new Reference(self::FILTER_CONFIGURATION),
            new Reference(self::PROCESSOR_PROVIDER),
        ]);
        $container->addDefinitions([self::PROXY_FILTER_CONFIGURATOR => $proxyFilterConfigurationDefinition]);

        $definition = $container->getDefinition(self::MESSAGE_PROCESSOR);
        $definition->replaceArgument(5, new Reference(self::PROXY_FILTER_CONFIGURATOR));
    }
}
