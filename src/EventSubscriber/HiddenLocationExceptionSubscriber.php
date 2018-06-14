<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\Core\MVC\Exception\HiddenLocationException;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HiddenLocationExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
     */
    private $responseTagger;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator
     */
    private $responseConfigurator;

    /**
     * @var \Symfony\Bundle\TwigBundle\Controller\ExceptionController
     */
    private $exceptionController;

    public function __construct(ResponseCacheConfigurator $responseConfigurator, LocationTagger $responseTagger, ExceptionController $exceptionController)
    {
        $this->responseTagger = $responseTagger;
        $this->responseConfigurator = $responseConfigurator;
        $this->exceptionController = $exceptionController;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => ['tagHiddenLocationExceptionResponse', 10]];
    }

    public function tagHiddenLocationExceptionResponse(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof HiddenLocationException) {
            return;
        }

        /** @var HiddenLocationException $exception */
        $exception = $event->getException();

        $response = $this->exceptionController->showAction($event->getRequest(), FlattenException::create($exception));
        $this->responseTagger->tag($this->responseConfigurator, $response, $exception->getLocation());

        $event->setResponse($response);
    }
}
