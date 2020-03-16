<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\Handler\ContentTagInterface;

class LocationTagger extends AbstractValueTagger
{
    public function tag($value)
    {
        if (!$value instanceof Location) {
            return $this;
        }

        if ($value->id !== $value->contentInfo->mainLocationId) {
            $this->responseTagger->addTags([ContentTagInterface::LOCATION_PREFIX . $value->id]);
        }

        $this->responseTagger->addTags([ContentTagInterface::PARENT_LOCATION_PREFIX . $value->parentLocationId]);
        $this->responseTagger->addTags(
            array_map(
                static function ($pathItem) {
                    return ContentTagInterface::PATH_PREFIX . $pathItem;
                },
                $value->path
            )
        );
    }
}
