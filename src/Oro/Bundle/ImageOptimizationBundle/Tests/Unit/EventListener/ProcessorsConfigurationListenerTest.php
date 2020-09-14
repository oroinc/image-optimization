<?php

namespace Oro\Bundle\ImageOptimizationBundle\Tests\Unit\EventListener;

use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Bundle\ImageOptimizationBundle\EventListener\ProcessorsConfigurationListener;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProcessorsConfigurationListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ProcessorsConfigurationListener */
    private $processorsConfigurationListener;

    /** @var Session|\PHPUnit\Framework\MockObject\MockObject */
    private $session;

    /** @var TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $translator;

    protected function setUp(): void
    {
        $this->session = $this->createMock(Session::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->processorsConfigurationListener = new ProcessorsConfigurationListener(
            $this->session,
            $this->translator
        );
    }

    /**
     * Check case when save the configuration with default settings after installing or upgrading platform.
     */
    public function testOnConfigUpdate(): void
    {
        $event = new ConfigUpdateEvent([]);
        $this->session
            ->expects($this->never())
            ->method('getFlashBag');
        $this->processorsConfigurationListener->onConfigUpdate($event);
    }

    public function testOnConfigChanged(): void
    {
        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('oro.image_optimization.config.quality_notify')
            ->willReturn('Translated message');

        $flashBag = $this->createMock(FlashBagInterface::class);
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('warning', 'Translated message');

        $this->session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $event = new ConfigUpdateEvent([
            'oro_image_optimization.png_quality' => 'value',
            'oro_image_optimization.jpeg_quality' => 'value'
        ]);

        $this->processorsConfigurationListener->onConfigUpdate($event);
    }
}
