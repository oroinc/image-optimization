<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\Provider;

use Oro\Bundle\AttachmentBundle\Provider\AttachmentFilterAwareUrlGenerator as BaseAttachmentFilterAwareUrlGenerator;
use Oro\Bundle\ImageOptimizationBundle\Configurator\Provider\HashProvider;
use Oro\Bundle\ImageOptimizationBundle\Provider\FilterAwareUrlGenerator;
use Oro\Bundle\TestFrameworkBundle\Test\Logger\LoggerAwareTraitTestTrait;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class FilterAwareUrlGeneratorTest extends \PHPUnit\Framework\TestCase
{
    use LoggerAwareTraitTestTrait;

    /** @var UrlGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlGenerator;

    /** @var HashProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $hashProvider;

    /** @var BaseAttachmentFilterAwareUrlGenerator|\PHPUnit\Framework\MockObject\MockObject */
    private $filterAwareGeneratorInner;

    /** @var FilterAwareUrlGenerator */
    private $filterAwareGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->hashProvider = $this->createMock(HashProvider::class);
        $this->filterAwareGeneratorInner = $this->createMock(BaseAttachmentFilterAwareUrlGenerator::class);

        $this->filterAwareGenerator = new FilterAwareUrlGenerator(
            $this->urlGenerator,
            $this->hashProvider,
            $this->filterAwareGeneratorInner
        );

        $this->setUpLoggerMock($this->filterAwareGenerator);
    }

    public function testSetContext(): void
    {
        /** @var RequestContext|\PHPUnit\Framework\MockObject\MockObject $context */
        $context = $this->createMock(RequestContext::class);
        $this->filterAwareGeneratorInner
            ->expects($this->once())
            ->method('setContext')
            ->with($context);

        $this->filterAwareGenerator->setContext($context);
    }

    public function testGetContext(): void
    {
        /** @var RequestContext|\PHPUnit\Framework\MockObject\MockObject $context */
        $context = $this->createMock(RequestContext::class);
        $this->filterAwareGeneratorInner
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        $this->assertSame($context, $this->filterAwareGenerator->getContext());
    }

    public function testGenerateWithoutFilter(): void
    {
        $route = 'test';
        $parameters = ['id' => 1];

        $this->hashProvider
            ->expects($this->never())
            ->method($this->anything());

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn('/test/1');

        $this->assertSame('/test/1', $this->filterAwareGenerator->generate($route, $parameters));
    }

    public function testGenerateWithFilter()
    {
        $route = 'test';
        $parameters = ['id' => 1, 'filter' => 'test_filter'];

        $filterHash = md5(json_encode(['size' => ['height' => 'auto']]));
        $this->hashProvider
            ->expects($this->once())
            ->method('getFilterConfigHash')
            ->with('test_filter')
            ->willReturn($filterHash);

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, ['id' => 1, 'filter' => 'test_filter', 'filterMd5' => $filterHash])
            ->willReturn('/test/1');

        $this->assertSame('/test/1', $this->filterAwareGenerator->generate($route, $parameters));
    }

    public function testGenerateWhenException(): void
    {
        $route = 'test';
        $parameters = ['id' => 1];

        $this->hashProvider
            ->expects($this->never())
            ->method($this->anything());

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willThrowException(new InvalidParameterException());

        $this->assertLoggerWarningMethodCalled();
        $this->assertEquals('', $this->filterAwareGenerator->generate($route, $parameters));
    }

    public function testGenerateWhenGeneratorReturnsNull(): void
    {
        $route = 'test';
        $parameters = ['id' => 1];

        $this->hashProvider
            ->expects($this->never())
            ->method($this->anything());

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn(null);

        $this->assertLoggerNotCalled();
        $this->assertEquals('', $this->filterAwareGenerator->generate($route, $parameters));
    }

    public function testGetFilterHash(): void
    {
        $filterName = 'filterName';
        $filterHash = md5(json_encode(['filterConfig']));
        $this->hashProvider
            ->expects($this->once())
            ->method('getFilterConfigHash')
            ->with($filterName)
            ->willReturn($filterHash);

        $this->assertEquals($filterHash, $this->filterAwareGenerator->getFilterHash($filterName));
    }
}
