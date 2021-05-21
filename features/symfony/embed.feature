@symfonycache
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
      | Header          | Value                  |
      | Cache-Control   | public, s-maxage=86400 |
      | X-Symfony-Cache | <headerValue>          |
    When I edit "<embeddedItemName>" Content item in "eng-GB"
      | name                     |
      | <editedEmbeddedItemName> | 
    And I reload the page
    And I reload the page
    Then I should see "<embeddingItemName>"
    And I should see "<editedEmbeddedItemName>"

    Examples:
      | user      | password | embeddingItemName  | embeddedItemName  | editedEmbeddedItemName  | headerValue                         |
      | admin     | publish  | EmbeddingItemAdmin | AdminEmbeddedItem | EditedEmbeddedItemAdmin | GET /site/embeddingitemadmin: fresh |
      | anonymous |          | EmbeddingItemAnon  | AnonEmbeddedItem  | EditedEmbeddedItemAnon  | GET /site/embeddingitemanon: fresh  |


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
      | Header          | Value                  |
      | Cache-Control   | public, s-maxage=86400 |
    And response headers match pattern
      | Header          | Pattern            |
      | X-Symfony-Cache | <expectedPattern>  |

    Examples:
      | embeddingItemName  | embeddedItemName  | expectedPattern |
      | EmbeddingItemNoEsi | EmbeddedItemNoEsi | /GET \/site\/embeddingitemnoesi\: fresh/ |
      | EmbeddingItemEsi   | EmbeddedItemEsi   | /GET \/site\/embeddingitemesi\: fresh; GET \/_fragment\?_hash\=.*%3D&_path\=contentId%3D53%26viewType%3Dline%26_format%3Dhtml%26_locale%3Den_GB%26_controller%3Dez_content%253A%253AviewAction\: fresh/ |
