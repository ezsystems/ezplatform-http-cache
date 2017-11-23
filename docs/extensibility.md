# Extending ezplatform-http-cache

## Registering custom configuration
This package defines the `http_cache` configuration blocks of the ezplatform semantic configuration:

```yaml
ezpublish:
  system:
    default:
      http_cache:
        purge_servers: []
```

For extensions that require extra siteaccess aware configuration for HTTP cache features,
an extension points exists. Instead of registering a new Config Parser with the `ezpublish`
container extension, one should do so on the `ezplatform_http_cache` one.

```php
class MyCustomBundle
{
  public function build(ContainerBuilder $container)
  {
    $cacheExtension = $container->getExtension('ez_platform_http_cache');
    $cacheExtension->addExtraConfigParser(new CustomConfigParser());
  }
}
```

```php
namespace MyCustomBundle\DependencyInjection\ConfigResolver;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface;

class CustomConfigParser implements ParserInterface
{
  public function addSemanticConfig(NodeBuilder $nodeBuilder)
  {
    $nodeBuilder->addScalarNode('custom_setting')->end();
  }

  public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
  {
    if (isset($scopeSettings['foo'])) {
      $contextualizer->setContextualParameter('http_cache.foo', $currentScope, $scopeSettings['foo']);
    }
  }
}
```

The `ParserInterface` implementation (`CustomConfigParser`) plays the same role than a regular one, with
two exceptions:
- in `addSemanticConfig()`, items are added to the `http_cache` node.
- in `mapConfig()`, the provided `$scopeSettings` contains the *contents* of the `http_cache` configuration key.

With the example above, `bin/console config:dump-reference ezpublish` will contain:

```yaml
ezpublish:
    system:
        siteaccess_name:
            http_cache:

                # This is defined by ezplatform-http-cache
                purge_servers:

                    # Examples:
                    - http://localhost/
                    - http://another.server/```
                # This is defined by the example
                foo: ~
```
