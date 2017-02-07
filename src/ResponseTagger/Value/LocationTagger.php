<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\Repository\Values\Content\Location;

class LocationTagger implements ResponseTagger
{
    public function tag(ResponseCacheConfigurator $configurator, Response $response, $value)
    {
        if (!$value instanceof Location) {
            return $this;
        }

        if ($value->id !== $value->contentInfo->mainLocationId) {
            $configurator->addTags($response, ['location-' . $value->id]);
        }

        $configurator->addTags($response, ['parent-' . $value->parentLocationId]);
        $configurator->addTags(
            $response,
            array_map(
                function ($pathItem) {
                    return 'path-' . $pathItem;
                },
                $value->path
            )
        );

        return $this;
    }
}
