<?php

namespace EzSystems\PlatformHttpCacheBundle\TagProvider;

final class ShortToLongTagConverter
{
    const SHORT_MAPPING = 'short';
    const LONG_MAPPING = 'long';

    /**
     * @var array
     */
    private $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function convert($tag)
    {
        $tagElements = \explode(TagProviderInterface::DELIMITER, $tag);

        // Given tag is already in long format, skipping.
        if (isset($tagElements[0]) && strlen($tagElements[0]) > 3) {
            return $tag;
        }

        $tagValue = \array_pop($tagElements);
        $tagKey = \implode(TagProviderInterface::DELIMITER, $tagElements);

        foreach ($this->mapping[self::SHORT_MAPPING] as $key => $value) {
            if ($tagKey === $value) {
                return $this->mapping[self::LONG_MAPPING][$key] . TagProviderInterface::DELIMITER . $tagValue;
            }
        }

        @trigger_error(
            "Could not convert {$tag} from short to long format",
            E_USER_WARNING
        );
    }
}
