<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Content\CopyContentEvent;
use eZ\Publish\API\Repository\Events\Content\CreateContentDraftEvent;
use eZ\Publish\API\Repository\Events\Content\DeleteContentEvent;
use eZ\Publish\API\Repository\Events\Content\DeleteVersionEvent;
use eZ\Publish\API\Repository\Events\Content\HideContentEvent;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;
use eZ\Publish\API\Repository\Events\Content\RevealContentEvent;
use eZ\Publish\API\Repository\Events\Content\UpdateContentEvent;
use eZ\Publish\API\Repository\Events\Content\UpdateContentMetadataEvent;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Handler as ContentHandler;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\URL\Handler as UrlHandler;
use EzSystems\PlatformHttpCacheBundle\Handler\ContentTagInterface;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;

final class ContentEventsSubscriber extends AbstractSubscriber
{
    /** @var \eZ\Publish\SPI\Persistence\Content\Handler */
    private $contentHandler;

    /** @var bool */
    private $isTranslationAware;

    public function __construct(
        PurgeClientInterface $purgeClient,
        LocationHandler $locationHandler,
        UrlHandler $urlHandler,
        ContentHandler $contentHandler,
        bool $isTranslationAware
    ) {
        parent::__construct($purgeClient, $locationHandler, $urlHandler);

        $this->isTranslationAware = $isTranslationAware;
        $this->contentHandler = $contentHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CopyContentEvent::class => 'onCopyContentEvent',
            CreateContentDraftEvent::class => 'onCreateContentDraftEvent',
            DeleteContentEvent::class => 'onDeleteContentEvent',
            DeleteVersionEvent::class => 'onDeleteVersionEvent',
            HideContentEvent::class => 'onHideContentEvent',
            PublishVersionEvent::class => 'onPublishVersionEvent',
            RevealContentEvent::class => 'onRevealContentEvent',
            UpdateContentEvent::class => 'onUpdateContentEvent',
            UpdateContentMetadataEvent::class => 'onUpdateContentMetadataEvent',
        ];
    }

    public function onCopyContentEvent(CopyContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;
        $parentLocationId = $event->getDestinationLocationCreateStruct()->parentLocationId;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_PREFIX . $contentId,
            ContentTagInterface::LOCATION_PREFIX . $parentLocationId,
            ContentTagInterface::PATH_PREFIX . $parentLocationId,
        ]);
    }

    public function onCreateContentDraftEvent(CreateContentDraftEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onDeleteContentEvent(DeleteContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = $this->getContentTags($contentId);

        foreach ($event->getLocations() as $locationId) {
            $tags[] = ContentTagInterface::PATH_PREFIX . $locationId;
        }

        $this->purgeClient->purge($tags);
    }

    public function onDeleteVersionEvent(DeleteVersionEvent $event): void
    {
        $contentId = $event->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onHideContentEvent(HideContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onPublishVersionEvent(PublishVersionEvent $event): void
    {
        $content = $event->getContent();
        $versionInfo = $content->getVersionInfo();
        $contentType = $content->getContentType();
        $contentId = $versionInfo->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $initialLanguageCode = $versionInfo->getInitialLanguage()->languageCode;
        $mainLanguageCode = $versionInfo->getContentInfo()->mainLanguageCode;

        $isNewTranslation = true;
        try {
            $prevVersionInfo = $this->contentHandler->loadVersionInfo($contentId, $event->getVersionInfo()->getContentInfo()->currentVersionNo);
            $isNewTranslation = !in_array($initialLanguageCode, $prevVersionInfo->languageCodes);
        } catch (NotFoundException $e) {
        }

        if (
            !$this->isTranslationAware ||
            $isNewTranslation ||
            ($initialLanguageCode === $mainLanguageCode && !$this->isContentTypeFullyTranslatable($contentType))
        ) {
            $this->purgeClient->purge($tags);

            return;
        }

        $tags = array_map(static function (string $tag) use ($initialLanguageCode): string {
            return $tag . $initialLanguageCode;
        }, $tags);
        $tags[] = ContentTagInterface::CONTENT_ALL_TRANSLATIONS_PREFIX . $contentId;

        $this->purgeClient->purge($tags);
    }

    public function onRevealContentEvent(RevealContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onUpdateContentEvent(UpdateContentEvent $event): void
    {
        $contentId = $event->getContent()->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onUpdateContentMetadataEvent(UpdateContentMetadataEvent $event): void
    {
        $contentId = $event->getContent()->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge(
            $this->getContentTags($contentId)
        );
    }

    private function isContentTypeFullyTranslatable(ContentType $contentType): bool
    {
        return !$contentType->getFieldDefinitions()->any(static function (FieldDefinition $fieldDefinition): bool {
            return !$fieldDefinition->isTranslatable;
        });
    }
}
