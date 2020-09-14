<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Functional\Provider;

use Oro\Bundle\AttachmentBundle\Provider\AttachmentFilterAwareUrlGenerator;
use Oro\Bundle\ImageOptimizationBundle\Configurator\FilterConfiguration;
use Oro\Bundle\ImageOptimizationBundle\Tests\Functional\Configurator\SettingsTrait;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolationPerTest
 */
class FilterAwareUrlGeneratorTest extends WebTestCase
{
    use SettingsTrait;

    private const FILTER_NAME = 'avatar_med';

    /** @var AttachmentFilterAwareUrlGenerator */
    private $attachmentUrlGenerator;

    protected function setUp(): void
    {
        $this->initClient([], $this->generateWsseAuthHeader());
        $this->attachmentUrlGenerator =
            $this->getContainer()->get('oro_image_optimization.url_generator');
    }

    public function testWithLibrariesExistsAndConfigNotChanged(): void
    {
        // Libraries exists but quality parameters was not changed
        // Always accept the configuration(post_processor => [...]) for the filter if libraries exist
        // and the configuration has been changed!
        $filterParameters = array_merge($this->getAvatarMedConfig(), ['post_processors' => []]);
        $url = $this->attachmentUrlGenerator->generate('oro_filtered_attachment', [
            'id' => '1',
            'filter' => self::FILTER_NAME,
            'filename' => 'filename'
        ]);

        $this->assertStringContainsString($this->getHash($filterParameters), $url);
    }


    public function testWithLibrariesExistsAndConfigChanged(): void
    {
        $this->changeQualityParameters(50, 50);
        $filterParameters = array_merge(
            $this->getAvatarMedConfig(),
            [
                'post_processors' => [
                    'pngquant' => ['quality' => 50],
                    'jpegoptim' => ['strip_all' => true, 'max' => 50, 'progressive' => false],
                ]
            ]
        );
        $url = $this->attachmentUrlGenerator->generate('oro_filtered_attachment', [
            'id' => '1',
            'filter' => self::FILTER_NAME,
            'filename' => 'filename'
        ]);

        $this->assertStringContainsString($this->getHash($filterParameters), $url);
    }

    /**
     * @return array
     */
    private function getAvatarMedConfig(): array
    {
        /** @var FilterConfiguration $attachmentFilterConfiguration */
        $attachmentFilterConfiguration =
            $this->getContainer()->get('oro_image_optimization.configurator.filter_configuration');

        return $attachmentFilterConfiguration->getOriginal(self::FILTER_NAME);
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function getHash(array $parameters): string
    {
        return md5(json_encode($parameters));
    }
}
