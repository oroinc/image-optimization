<?php

namespace Oro\Bundle\ImageOptimizationBundle\Checker\Voter;

use Oro\Bundle\FeatureToggleBundle\Checker\Voter\VoterInterface;
use Oro\Bundle\ImageOptimizationBundle\ProcessorHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Checks whether libraries are present in the system.
 */
class PostProcessorsVoter implements VoterInterface
{
    public const POST_PROCESSORS = 'post_processors';

    /**
     * @var null|string
     */
    private $jpegopim;

    /**
     * @var null|string
     */
    private $pngQuant;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param string|null $jpegopim
     * @param string|null $pngQuant
     */
    public function __construct(LoggerInterface $logger, ?string $jpegopim, ?string $pngQuant)
    {
        $this->logger = $logger;
        $this->jpegopim = $jpegopim;
        $this->pngQuant = $pngQuant;
    }

    /**
     * @inhericDoc
     */
    public function vote($feature, $scopeIdentifier = null): int
    {
        if ($feature === self::POST_PROCESSORS) {
            $processorHelper = new ProcessorHelper($this->getParameters());
            try {
                $librariesExists = $processorHelper->librariesExists();
            } catch (\Exception $exception) {
                $this->logger->log(LogLevel::ERROR, $exception->getMessage());

                return self::FEATURE_DISABLED;
            }

            return $librariesExists ? self::FEATURE_ENABLED : self::FEATURE_DISABLED;
        }

        return self::FEATURE_ABSTAIN;
    }

    /**
     * @return ParameterBag
     */
    private function getParameters(): ParameterBag
    {
        return new ParameterBag([
            'liip_imagine.jpegoptim.binary' => $this->jpegopim,
            'liip_imagine.pngquant.binary' => $this->pngQuant
        ]);
    }
}
