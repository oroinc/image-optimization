<?php

namespace Oro\Bundle\ImageOptimizationBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for ImageOptimizationBundle.
 */
class Configuration implements ConfigurationInterface
{
    public const JPEG_QUALITY = 85;
    public const PNG_QUALITY = 100;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oro_image_optimization');
        $rootNode
            ->children()
                ->integerNode('png_quality')
                    ->min(1)
                    ->max(100)
                    ->defaultValue(self::PNG_QUALITY)
                ->end()
                ->integerNode('jpeg_quality')
                    ->min(30)
                    ->max(100)
                    ->defaultValue(self::JPEG_QUALITY)
                ->end()
            ->end();

        SettingsBuilder::append(
            $rootNode,
            [
                'jpeg_quality' => ['value' => self::JPEG_QUALITY],
                'png_quality' => ['value' => self::PNG_QUALITY],
            ]
        );

        return $treeBuilder;
    }
}
