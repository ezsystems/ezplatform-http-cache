<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Symfony\Component\HttpFoundation\Response;

class ContentInfoTagger implements ResponseTagger
{
    public function tag(ResponseCacheConfigurator $configurator, Response $response, $value)
    {
        if (!$value instanceof ContentInfo) {
            return $this;
        }

        $configurator->addTags(
            $response,
            ['content-' . $value->id, 'content-type-' . $value->contentTypeId]
        );

        if ($value->mainLocationId) {
            $configurator->addTags($response, ['location-' . $value->mainLocationId]);
        }
    }
}
