<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\Configurator\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Bundle\ImageOptimizationBundle\Checker\Voter\PostProcessingVoter;
use Oro\Bundle\ImageOptimizationBundle\Checker\Voter\PostProcessorsVoter;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;
use Oro\Bundle\ImageOptimizationBundle\DependencyInjection\Configuration;

class ProcessorsProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var ProcessorsProvider */
    private $processorsProvider;

    /** @var ConfigManager|\PHPUnit\Framework\MockObject\MockObject */
    private $configManager;

    /** @var FeatureChecker|\PHPUnit\Framework\MockObject\MockObject */
    private $featureChecker;

    protected function setUp(): void
    {
        $this->configManager = $this->createMock(ConfigManager::class);
        $this->featureChecker = $this->createMock(FeatureChecker::class);
        $this->processorsProvider = new ProcessorsProvider($this->configManager);
        $this->processorsProvider->setFeatureChecker($this->featureChecker);
    }

    /**
     * @dataProvider postProcessingProvider
     *
     * @param bool $postProcessingEnabled
     */
    public function testPostProcessingEnabled(bool $postProcessingEnabled): void
    {
        $this->featureChecker
            ->expects($this->once())
            ->method('isFeatureEnabled')
            ->with(PostProcessingVoter::POST_PROCESSING)
            ->willReturn($postProcessingEnabled);

        $this->assertEquals($postProcessingEnabled, $this->processorsProvider->isPostProcessingEnabled());
        // Need to check whether the parameters are saved locally
        $this->assertEquals($postProcessingEnabled, $this->processorsProvider->isPostProcessingEnabled());
    }

    /**
     * @return array
     */
    public function postProcessingProvider(): array
    {
        return [
            'Post processing feature enable' => [
                'Processing status' => true,
            ],
            'Post processing feature disable' => [
                'Processing status' => false,
            ],
        ];
    }

    /**
     * @dataProvider postProcessorProvider
     *
     * @param bool $postProcessorEnabled
     */
    public function testPostProcessorEnabled(bool $postProcessorEnabled): void
    {
        $this->featureChecker
            ->expects($this->once())
            ->method('isFeatureEnabled')
            ->with(PostProcessorsVoter::POST_PROCESSORS)
            ->willReturn($postProcessorEnabled);

        $this->assertEquals($postProcessorEnabled, $this->processorsProvider->isPostProcessorEnabled());
        // Need to check whether the parameters are saved locally
        $this->assertEquals($postProcessorEnabled, $this->processorsProvider->isPostProcessorEnabled());
    }

    /**
     * @return array
     */
    public function postProcessorProvider(): array
    {
        return [
            'Post processor feature enable' => [
                'post_processor_enabled' => true,
            ],
            'Post processor feature disabled' => [
                'post_processor_enabled' => false,
            ],
        ];
    }

    public function testGetFilterConfig(): void
    {
        $this->featureChecker
            ->expects($this->once())
            ->method('isFeatureEnabled')
            ->with(PostProcessorsVoter::POST_PROCESSORS)
            ->willReturn(true);

        $this->configManager
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['oro_image_optimization.png_quality'],
                ['oro_image_optimization.jpeg_quality']
            )
            ->willReturnOnConsecutiveCalls(
                Configuration::PNG_QUALITY,
                Configuration::JPEG_QUALITY
            );

        $expected = [
            'pngquant' => ['quality' => Configuration::PNG_QUALITY],
            'jpegoptim' => ['strip_all' => true, 'max' => Configuration::JPEG_QUALITY, 'progressive' => false],
        ];

        $this->assertEquals($expected, $this->processorsProvider->getFilterConfig());
    }
}
