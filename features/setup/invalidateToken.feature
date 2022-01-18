@setup
Feature: Set system to use token for invalidation

  Scenario: Set up the system to test caching of subrequests
    Given I append configuration to "parameters"
    """
        varnish_invalidate_token: 'TESTTOKEN'
    """
