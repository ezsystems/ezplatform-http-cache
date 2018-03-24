<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Symfony\Component\HttpFoundation\Response;

class ViewParametersTagger implements ResponseTagger
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $dispatcherTagger;

    public function __construct(ResponseTagger $dispatcherTagger)
    {
        $this->dispatcherTagger = $dispatcherTagger;
    }

    public function tag(ResponseCacheConfigurator $configurator, Response $response, $view)
    {
        if (!$view instanceof View) {
            return $this;
        }

        foreach ($view->getParameters() as $parameter) {
            if (!$parameter instanceof ValueObject) {
                continue;
            }

            $this->dispatcherTagger->tag($configurator, $response, $parameter);
        }

        return $this;
    }
}
