<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\REST\Common\Values\Root;
use eZ\Publish\Core\REST\Server\Values\CachedValue;
use eZ\Publish\Core\REST\Server\Values\ContentTypeGroupList;
use eZ\Publish\Core\REST\Server\Values\ContentTypeGroupRefList;
use eZ\Publish\Core\REST\Server\Values\RestContentType;
use eZ\Publish\Core\REST\Server\Values\VersionList;
use EzSystems\MultiFileUpload\API\Repository\Values\PermissionReport;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use FOS\HttpCache\Handler\TagHandler;

/**
 * Set cache tags on a few REST responses used by UI in order to be able to cache them.
 *
 * @deprecated This is a temprary approach to caching certain parts of REST used by UI, it is deprecated in favour of
 *             nativly using tags and CachedValue in kernel's REST server itself once we switch to FOSHttpCache 2.x
 *             where tagger service can be used directly.
 */
class RestKernelViewSubscriber implements EventSubscriberInterface
{
    /** @var \FOS\HttpCache\Handler\TagHandler */
    private $tagHandler;

    public function __construct(TagHandler $tagHandler)
    {
        $this->tagHandler = $tagHandler;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['tagUIRestResult', 10]];
    }

    public function tagUIRestResult(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->isMethodCacheable() || !$request->attributes->get('is_rest_request')) {
            return;
        }

        // Get tags, and exit if none where found
        $restValue = $event->getControllerResult();
        $tags = $this->getTags($restValue);
        if (empty($tags)) {
            return;
        }

        // Add tags and swap Rest Value for cached value now that REST server can safely cache it
        $this->tagHandler->addTags($tags);
        $event->setControllerResult(new CachedValue($restValue));
    }

    /**
     * @param object $value
     *
     * @return array
     */
    protected function getTags($value)
    {
        $tags = [];
        switch ($value) {
            case $value instanceof VersionList && !empty($value->versions):
                $tags[] = 'content-' . $value->versions[0]->contentInfo->id;
                $tags[] = 'content-versions-' . $value->versions[0]->contentInfo->id;

                break;

            case $value instanceof Section:
                $tags[] = 'section-' . $value->id;
                break;

            case $value instanceof ContentTypeGroupRefList:
                if ($value->contentType->status !== ContentType::STATUS_DEFINED) {
                    return [];
                }
                $tags[] = 'type-' . $value->contentType->id;
            case $value instanceof ContentTypeGroupList:
                foreach ($value->contentTypeGroups as $contentTypeGroup) {
                    $tags[] = 'type-group-' . $contentTypeGroup->id;
                }
                break;

            case $value instanceof RestContentType:
                $value = $value->contentType;
            case $value instanceof ContentType:
                if ($value->status !== ContentType::STATUS_DEFINED) {
                    return [];
                }
                $tags[] = 'type-' . $value->id;
                break;

            case $value instanceof Root:
                $tags[] = 'ez-all';
                break;

                // @deprecated The following logic is 1.x specific, and should be removed before a 1.0 version
            case $value instanceof PermissionReport:
                // We requrie v0.1.5 with added location property to be able to add tags
                if (!isset($value->parentLocation)) {
                    return [];
                }

                // In case of for instance location swap where content type might change affecting allowed content types
                $tags[] = 'content-' . $value->parentLocation->contentId;
                $tags[] = 'content-type-' . $value->parentLocation->contentInfo->contentTypeId;
                $tags[] = 'location-' . $value->parentLocation->id;

                // In case of permissions assigned by subtree, so if path changes affecting this (move subtree operation)
                foreach ($value->parentLocation->path as $pathItem) {
                    $tags[] = 'path-' . $pathItem;
                }
                break;
        }

        return $tags;
    }
}
