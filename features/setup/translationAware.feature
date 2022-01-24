@setup @translationAware
Feature: Set system to use translation-aware cache invalidation

  Scenario: Set up the system to use translation-aware cache invalidation
    Given I append configuration to "parameters"
    """
        ibexa.http_cache.translation_aware.enabled: true
    """
