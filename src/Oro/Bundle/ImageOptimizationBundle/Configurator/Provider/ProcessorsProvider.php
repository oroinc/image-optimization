<?php

namespace Oro\Bundle\ImageOptimizationBundle\Configurator\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureCheckerHolderTrait;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureToggleableInterface;
use Oro\Bundle\ImageOptimizationBundle\Checker\Voter\PostProcessingVoter;
use Oro\Bundle\ImageOptimizationBundle\Checker\Voter\PostProcessorsVoter;

/**
 * Responsible for the correct creation of "post_processor" parameters for both binary processors and url hashes.
 */
class ProcessorsProvider implements FeatureToggleableInterface
{
    use FeatureCheckerHolderTrait;

    /**
     * @var array
     */
    private $postProcessorsConfigs = [];

    /**
     * @var null|bool
     */
    private $postProcessingEnabled = null;

    /**
     * @var null|bool
     */
    private $postProcessorEnabled = null;

    /**
     * @var ConfigManager
     */
    private $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return bool
     */
    public function isPostProcessingEnabled(): bool
    {
        if (null === $this->postProcessingEnabled) {
            $this->postProcessingEnabled
                = $this->featureChecker->isFeatureEnabled(PostProcessingVoter::POST_PROCESSING);
        }

        return $this->postProcessingEnabled;
    }

    /**
     * @return bool
     */
    public function isPostProcessorEnabled(): bool
    {
        if (null === $this->postProcessorEnabled) {
            $this->postProcessorEnabled
                = $this->featureChecker->isFeatureEnabled(PostProcessorsVoter::POST_PROCESSORS);
        }

        return $this->postProcessorEnabled;
    }

    /**
     * @return array
     */
    private function getSystemConfig(): array
    {
        if (!$this->postProcessorsConfigs) {
            $this->postProcessorsConfigs = [
                $this->configManager->get('oro_image_optimization.png_quality'),
                $this->configManager->get('oro_image_optimization.jpeg_quality'),
            ];
        }

        return $this->postProcessorsConfigs;
    }

    /**
     * @return array
     */
    public function getFilterConfig(): array
    {
        if ($this->isPostProcessorEnabled()) {
            [$pngQuality, $jpegQuality] = $this->getSystemConfig();

            return [
                'pngquant' => ['quality' => $pngQuality],
                'jpegoptim' => ['strip_all' => true, 'max' => $jpegQuality, 'progressive' => false],
            ];
        }

        return [];
    }
}
