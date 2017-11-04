<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license   For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Repository;

/**
 * This class conver the deprecated X-Location-Id in path-$locationId
 * It handles all the non-view responses when X-Location-Id is present.
 */
class XLocationIdBCTagger implements ResponseTagger
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ResponseTagger
     */
    private $locationTagger;

    const LOCATION_ID_HEADER = 'X-Location-Id';

    public function __construct(
        Repository $repository,
        ResponseTagger $locationTagger
    ) {
        $this->repository        = $repository;
        $this->locationTagger    = $locationTagger;
    }

    public function tag(ResponseCacheConfigurator $configurator, Response $response, $view)
    {
        if ($view !== null || !$response->headers->has(static::LOCATION_ID_HEADER)) {
            return null;
        }

        $locationIds = explode("|", $response->headers->get(static::LOCATION_ID_HEADER));
        foreach ($locationIds as $locationId) {
            try {
                $location = $this->repository->getLocationService()->loadLocation($locationId);
                $this->locationTagger->tag($configurator, $response, $location);
                $response->headers->remove(static::LOCATION_ID_HEADER);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        return $this;
    }
}
