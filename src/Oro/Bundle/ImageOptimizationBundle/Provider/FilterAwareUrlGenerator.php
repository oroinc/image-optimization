<?php

namespace Oro\Bundle\ImageOptimizationBundle\Provider;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Oro\Bundle\AttachmentBundle\Provider\AttachmentFilterAwareUrlGenerator as BaseAttachmentFilterAwareUrlGenerator;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\HashProvider;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\ProcessorsProvider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * URL generator for files. Adds filterMd5 to parameters when filter is present.
 */
class FilterAwareUrlGenerator implements UrlGeneratorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var BaseAttachmentFilterAwareUrlGenerator
     */
    private $attachmentFilterAwareUrlGenerator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FilterConfiguration
     */
    private $filterConfiguration;

    /**
     * @var HashProvider
     */
    private $hashProvider;

    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param HashProvider $hashProvider
     * @param BaseAttachmentFilterAwareUrlGenerator $attachmentFilterAwareUrlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        HashProvider $hashProvider,
        BaseAttachmentFilterAwareUrlGenerator $attachmentFilterAwareUrlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->hashProvider = $hashProvider;
        $this->attachmentFilterAwareUrlGenerator = $attachmentFilterAwareUrlGenerator;

        $this->logger = new NullLogger();
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param int $referenceType
     *
     * @return string
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        if (!empty($parameters['filter'])) {
            $parameters['filterMd5'] = $this->getFilterHash($parameters['filter']);
        }

        $url = '';
        try {
            $url = (string)$this->urlGenerator->generate($name, $parameters, $referenceType);
            // Catches only InvalidParameterException because it is the only one that can be caused during normal
            // runtime, other exceptions should lead to direct fix.
        } catch (InvalidParameterException $e) {
            $message = 'Failed to generate file url by route "%s" with parameters: %s';
            $this->logger->warning(sprintf($message, $name, json_encode($parameters)), ['e' => $e]);
        }

        return $url;
    }

    /**
     * Important: To maintain backward compatibility, to build hash, do not use 'post_processors' parameter unless the
     * system configuration has been changed.
     *
     * Default processors configuration (described in 'dimensions' or 'liip_imagine' configuration) have a higher
     * priority than system configuration. To maintain backward compatibility with previous versions,
     * need to check which processors configuration are used in filter and cover the following cases:
     * - Processors configuration are exists, it is necessary use them and ignore system configuration.
     * - Keep backward compatibility. If processor configuration does not exist, then not need
     *   to update(add 'post_processors' configuration to filter) hash, provided that the new system configuration
     *   has default value.
     * - If the system configuration has changed and is not equivalent to the prevent(default) configuration,
     *   then build a hash with the 'post_processors' parameter.
     *
     * @param string $filterName
     *
     * @return string
     */
    public function getFilterHash(string $filterName): string
    {
        return $this->hashProvider->getFilterConfigHash($filterName);
    }

    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context): void
    {
        $this->attachmentFilterAwareUrlGenerator->setContext($context);
    }

    /**
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->attachmentFilterAwareUrlGenerator->getContext();
    }
}
