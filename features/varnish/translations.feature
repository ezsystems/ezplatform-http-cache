@varnish
Feature: As an site administrator I want my pages to be cached using Varnish

    @APIUser:admin
    Scenario Outline: Correct translation is displayed when a new translation is published
        Given I create "Folder" Content items in root in "pol-PL"
            | name       | short_name |
            | TestFolder | <itemName> |
        And I am viewing the pages on siteaccess "site" as "<user>" "<password>"
        And I visit "<itemName>" on siteaccess "site"
        And I reload the page
        And I see correct preview data for "Folder" Content Type
            | field | value      |
            | title | <itemName> |
        And response headers contain
            | Header  | Value |
            | X-Cache | HIT   |
        When I edit "<itemName>" Content item in "eng-GB"
            | short_name          |
            | <itemNameAfterEdit> |
        And I reload the page
        # Give Varnish time to fetch the backend response
        And I wait 5 seconds
        # Second reload is needed because of soft purging
        And I reload the page
        And I reload the page
        Then I see correct preview data for "Folder" Content Type
            | field | value               |
            | title | <itemNameAfterEdit> |
        And response headers contain
            | Header  | Value |
            | X-Cache | HIT   |

        Examples:
            | user      | password | itemName        | itemNameAfterEdit |
            | admin     | publish  | ItemPolskiAdmin | ItemEnglishAdmin  |
            | anonymous |          | ItemPolskiAnon  | ItemEnglishAnon   |

  @APIUser:admin @javascript @translationNotAware
  Scenario: Main translation cache is purged when a fallback translation is edited
    Given I am viewing the pages on siteaccess "site" as "admin" with password "publish"
    And I create "embeddedContentType" Content items in root in "eng-GB"
        | name                       |
        | EmbeddedTranslationEnglish |
    And I create "embeddingContentType_no_esi" Content items in root in "eng-GB"
        | name                        | relation                    |
        | EmbeddingTranslationEnglish | /EmbeddedTranslationEnglish |
    And I edit "EmbeddedTranslationEnglish" Content item in "fre-FR"
        | name                      |
        | EmbeddedTranslationFrench |
    And I start measuring time
    And I visit "/EmbeddingTranslationEnglish" on siteaccess "site"
    And the action took longer than 5 seconds
    And I should see "EmbeddingTranslationEnglish"
    And I should see "EmbeddedTranslationEnglish"
    And I start measuring time
    And I reload the page
    And the action took no longer than 1 seconds
    When I edit "EmbeddedTranslationEnglish" Content item in "fre-FR"
        | name                            |
        | EmbeddedTranslationFrenchEdited |
    # Give Varnish time to get purged
    And I wait 1 seconds
    And I start measuring time
    And I reload the page
    And I reload the page
    Then I should see "EmbeddingTranslationEnglish"
    And I should see "EmbeddedTranslationEnglish"
    And the action took longer than 5 seconds

  @APIUser:admin @javascript @translationAware
  Scenario: Main translation cache is not purged when a fallback translation is edited
    Given I am viewing the pages on siteaccess "site" as "admin" with password "publish"
    And I create "embeddedContentType" Content items in root in "eng-GB"
        | name                       |
        | EmbeddedTranslationEnglish |
    And I create "embeddingContentType_no_esi" Content items in root in "eng-GB"
        | name                        | relation                    |
        | EmbeddingTranslationEnglish | /EmbeddedTranslationEnglish |
    And I edit "EmbeddedTranslationEnglish" Content item in "fre-FR"
        | name                      |
        | EmbeddedTranslationFrench |
    And I start measuring time
    And I visit "/EmbeddingTranslationEnglish" on siteaccess "site"
    And the action took longer than 5 seconds
    And I should see "EmbeddingTranslationEnglish"
    And I should see "EmbeddedTranslationEnglish"
    And I start measuring time
    And I reload the page
    And the action took no longer than 1 seconds
    When I edit "EmbeddedTranslationEnglish" Content item in "fre-FR"
        | name                            |
        | EmbeddedTranslationFrenchEdited |
    # Give Varnish time to get purged
    And I wait 1 seconds
    And I start measuring time
    And I reload the page
    And I reload the page
    Then I should see "EmbeddingTranslationEnglish"
    And I should see "EmbeddedTranslationEnglish"
    And the action took no longer than 1 seconds
