<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Service RepositoryPrefix.
 *
 * @internal For use in EzSystems\PlatformHttpCacheBundle package.
 */
class RepositoryTagPrefix
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $resolver;

    /**
     * @var string
     */
    private $defaultRepository = '';

    public function __construct(ConfigResolverInterface $resolver, array $repositories)
    {
        $this->resolver = $resolver;

        // First repositories is the default one, foreach is apparently the fastest way to get it.
        foreach ($repositories as $repositoryId => $value) {
            $this->defaultRepository = $repositoryId;

            break;
        }
    }

    /**
     * Return repository prefix, either '' if default or '<repositoryId>_' if not default repository.
     *
     * WARNING: Must be called on-demand and not in constructors to avoid any issues with SiteAccess scope changes.
     *
     * @return string
     */
    public function getRepositoryPrefix()
    {
        $repositoryId = $this->resolver->getParameter('repository');

        return empty($repositoryId) || $repositoryId === $this->defaultRepository ? '' : $repositoryId . '_';
    }
}
