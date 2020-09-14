<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Functional\Configurator;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

trait SettingsTrait
{
    /**
     * @param int $jpegQuality
     * @param int $pngQuality
     */
    public function changeQualityParameters(int $jpegQuality = 85, int $pngQuality = 100): void
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->getContainer()->get('oro_config.global');
        $configManager->set('oro_image_optimization.jpeg_quality', $jpegQuality);
        $configManager->set('oro_image_optimization.png_quality', $pngQuality);
        $configManager->flush();
    }
}
