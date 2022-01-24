<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\HttpCache\EventSubscriber;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AddContentLanguageHeaderSubscriber implements EventSubscriberInterface
{
    public const CONTENT_LANGUAGE_HEADER = 'x-lang';

    /** @var bool */
    private $isTranslationAware;

    public function __construct(bool $isTranslationAware)
    {
        $this->isTranslationAware = $isTranslationAware;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$this->isTranslationAware || HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $view = $request->attributes->get('view');
        if (!$view instanceof CachableView || !$view->isCacheEnabled()) {
            return;
        }

        $content = $request->attributes->get('content');
        if ($content instanceof Content) {
            $event->getResponse()->headers->add([self::CONTENT_LANGUAGE_HEADER => $content->getDefaultLanguageCode()]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
