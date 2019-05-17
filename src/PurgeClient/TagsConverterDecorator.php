<?php

namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\TagProvider\ShortToLongTagConverter;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;

class TagsConverterDecorator implements PurgeClientInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface
     */
    private $purgeClient;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface
     */
    private $tagProvider;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\TagProvider\ShortToLongTagConverter
     */
    private $tagConverter;

    public function __construct(
        PurgeClientInterface $purgeClient,
        TagProviderInterface $tagProvider,
        ShortToLongTagConverter $tagMapper
    ) {
        $this->purgeClient = $purgeClient;
        $this->tagConverter = $tagMapper;
        $this->tagProvider = $tagProvider;
    }

    public function purge($tags)
    {
        $tags = $this->map($tags);
        $this->purgeClient->purge($tags);
    }

    public function purgeAll()
    {
        $this->purgeClient->purgeAll();
    }

    private function map(array $tags)
    {
        $userContextHash = $this->tagProvider->getTagForUserContextHash();
        $mappedTags = [];

        foreach (array_unique($tags) as $tag) {
            // If tag is numeric, do nothing. It's going to be handled properly in the next decorator.
            if (is_numeric($tag)) {
                continue;
            }

            // If tag is context hash, do nothing.
            if ($tag === $userContextHash) {
                continue;
            }

            $mappedTags[] = $this->tagConverter->convert($tag);
        }

        return array_unique(array_merge($tags, $mappedTags));
    }
}
