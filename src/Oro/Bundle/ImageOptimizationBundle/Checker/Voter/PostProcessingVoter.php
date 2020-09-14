<?php

namespace Oro\Bundle\ImageOptimizationBundle\Checker\Voter;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\FeatureToggleBundle\Checker\Voter\VoterInterface;
use Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Configuration;

/**
 * Indicates whether to use post processors.
 */
class PostProcessingVoter implements VoterInterface
{
    public const POST_PROCESSING = 'post_processing';

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @inhericDoc
     */
    public function vote($feature, $scopeIdentifier = null): int
    {
        if ($feature === self::POST_PROCESSING) {
            return $this->isDefaultQualityUsed() ? self::FEATURE_DISABLED : self::FEATURE_ENABLED;
        }

        return self::FEATURE_ABSTAIN;
    }

    /**
     * @return bool
     */
    private function isDefaultQualityUsed(): bool
    {
        $pngQuality = $this->configManager->get('oro_image_optimization.png_quality');
        $jpegQuality = $this->configManager->get('oro_image_optimization.jpeg_quality');

        return Configuration::PNG_QUALITY === $pngQuality && Configuration::JPEG_QUALITY === $jpegQuality;
    }
}
