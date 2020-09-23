<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\Checker\Voter;

use Oro\Bundle\FeatureToggleBundle\Checker\Voter\VoterInterface;
use Oro\Bundle\ImageOptimizationBundle\Checker\Voter\PostProcessorsVoter;
use Oro\Bundle\ImageOptimizationBundle\Tests\Unit\CheckProcessorsTrait;
use Psr\Log\LoggerInterface;

/**
 * The test checks the "feature voter", the results of which depend from external libraries: pngquant and jpegoptim.
 */
class PostProcessorsVoterTest extends \PHPUnit\Framework\TestCase
{
    use CheckProcessorsTrait;

    /** @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface  */
    private $logger;

    protected function setUp(): void
    {
        $this->checkProcessors();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->logger
            ->expects($this->never())
            ->method('log');
    }

    public function testVoteWithAnyFeature(): void
    {
        $postProcessorVoter = new PostProcessorsVoter($this->logger, null, null);

        $this->assertEquals(VoterInterface::FEATURE_ABSTAIN, $postProcessorVoter->vote('feature'));
    }

    public function testVote(): void
    {
        $postProcessorVoter = new PostProcessorsVoter($this->logger, null, null);
        $vote = $postProcessorVoter->vote(PostProcessorsVoter::POST_PROCESSORS);

        $this->assertEquals(VoterInterface::FEATURE_ENABLED, $vote);
    }
}
