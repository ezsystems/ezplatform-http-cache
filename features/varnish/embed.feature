@varnish6 @varnish7
Feature: Caching of embedded items

  @admin
  Scenario Outline: Editing an embedded item refreshes the embedding item as well
    Given I create a "embeddedContentType" Content Type in "Content" with "embeddedContentType" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	   | yes      | yes	       | yes          |
    And I create "embeddedContentType" Content items in root in "eng-GB"
      | name               |
      | <embeddedItemName> |
    And I create a "embeddingContentType" Content Type in "Content" with "embeddingContentType" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	   | yes      | yes	       | yes          |
      | Content relation (single) | Relation  | relation   | yes      | no	       | yes          |
    And I create "embeddingContentType" Content items in root in "eng-GB"
      | name                | relation            |
      | <embeddingItemName> | /<embeddedItemName> |
    And I am viewing the pages on siteaccess "site" as "<user>" with password "<password>"
    And I visit "/<embeddingItemName>" on siteaccess "site"
    And I reload the page
    And I should see "<embeddingItemName>"
    And I should see "<embeddedItemName>"
    And response headers contain
      | Header  | Value |
      | X-Cache | HIT   |
    When I edit "<embeddedItemName>" Content item in "eng-GB"
      | name                     |
      | <editedEmbeddedItemName> |
    And I reload the page
    # Give Varnish time to fetch the backend response
    And I wait 5 seconds
    # Second reload is needed because of soft purging
    And I reload the page
    Then I should see "<embeddingItemName>"
    And I should see "<editedEmbeddedItemName>"

    Examples:
      | user      | password | embeddingItemName  | embeddedItemName  | editedEmbeddedItemName  |
      | admin     | publish  | EmbeddingItemAdmin | AdminEmbeddedItem | EditedEmbeddedItemAdmin |
      | anonymous |          | EmbeddingItemAnon  | AnonEmbeddedItem  | EditedEmbeddedItemAnon  |

  Scenario Outline: Embedded requests are cached
    Given I am viewing the pages on siteaccess "site" as "admin" with password "publish"
    And I start measuring time
    And I visit "/<embeddingItemName>" on siteaccess "site"
    And the action took longer than 5 seconds
    And I should see "<embeddingItemName>"
    And I should see "<embeddedItemName>"
    When I start measuring time
    And I reload the page
    Then the action took no longer than 1 seconds
    And I should see "<embeddingItemName>"
    And I should see "<embeddedItemName>"
    And response headers contain
      | Header  | Value |
      | X-Cache | HIT   |

    Examples:
      | embeddingItemName  | embeddedItemName  |
      | EmbeddingItemNoEsi | EmbeddedItemNoEsi |
      | EmbeddingItemEsi   | EmbeddedItemEsi   |
