<?php

namespace Oro\Bundle\ImageOptimizationBundle\EventListener;

use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Render flash message if config changed.
 */
class ProcessorsConfigurationListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param ConfigUpdateEvent $event
     */
    public function onConfigUpdate(ConfigUpdateEvent $event)
    {
        if ($this->isConfigChanged($event)) {
            $this->addReindexWarningMessage();
        }
    }

    /**
     * @param ConfigUpdateEvent $event
     *
     * @return bool
     */
    private function isConfigChanged(ConfigUpdateEvent $event): bool
    {
        return
            $event->isChanged('oro_image_optimization.png_quality') ||
            $event->isChanged('oro_image_optimization.jpeg_quality');
    }

    private function addReindexWarningMessage(): void
    {
        $this->session->getFlashBag()->add(
            'warning',
            $this->translator->trans('oro.image_optimization.config.quality_notify')
        );
    }
}
