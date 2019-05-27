<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\ProxyClient;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\DynamicSettingParserInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class HttpDispatcherFactory
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\DynamicSettingParserInterface */
    private $dynamicSettingParser;

    /** @var string */
    private $httpDispatcherClass;

    public function __construct(
        ConfigResolverInterface $configResolver,
        DynamicSettingParserInterface $dynamicSettingParser,
        string $httpDispatcherClass
    ) {
        $this->configResolver = $configResolver;
        $this->dynamicSettingParser = $dynamicSettingParser;
        $this->httpDispatcherClass = $httpDispatcherClass;
    }

    public function buildHttpDispatcher(array $servers, string $baseUrl = '')
    {
        $allServers = array();
        foreach ($servers as $server) {
            if (!$this->dynamicSettingParser->isDynamicSetting($server)) {
                $allServers[] = $server;
                continue;
            }

            $settings = $this->dynamicSettingParser->parseDynamicSetting($server);
            $configuredServers = $this->configResolver->getParameter(
                $settings['param'],
                $settings['namespace'],
                $settings['scope']
            );
            $allServers = array_merge($allServers, (array)$configuredServers);
        }

        return new $this->httpDispatcherClass($allServers, $baseUrl);
    }
}
