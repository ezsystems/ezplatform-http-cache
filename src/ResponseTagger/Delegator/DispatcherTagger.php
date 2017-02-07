<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dispatches a value to all registered ResponseTaggers.
 */
class DispatcherTagger implements ResponseTagger
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $taggers = [];

    public function __construct(array $taggers = [])
    {
        $this->taggers = $taggers;
    }

    public function tag(ResponseCacheConfigurator $configurator, Response $response, $value)
    {
        foreach ($this->taggers as $tagger) {
            $tagger->tag($configurator, $response, $value);
        }
    }
}
