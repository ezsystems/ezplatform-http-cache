<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

/**
 * Trait RepositoryIdAwareTrait
 *
 * @internal For use in EzSystems\PlatformHttpCacheBundle package.
 */
trait RepositoryIdAwareTrait
{
    /** @var string */
    private $repoPrefix = '';

    public function setRepositoryId($repositoryId, array $repositories)
    {
        foreach ($repositories as $default => $value) {
            // Do nothing, we just wanted the first key which is the default repo in the fastest possible way
            break;
        }

        $this->repoPrefix = empty($repositoryId) || $repositoryId === $default ? '' : $repositoryId . '_';
    }
}
