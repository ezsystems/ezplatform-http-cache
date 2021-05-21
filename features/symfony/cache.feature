@symfonycache
Feature: As an site administrator I want my pages to be cached using Symfony Http Cache

  @admin
  Scenario Outline: Content Items are cached for user when visited
    Given I create "Folder" Content items in root in "eng-GB"
      | name       | short_name |
      | TestFolder | <itemName> |
    And I am viewing the pages on siteaccess "site" as <user> "<password>"
    When I visit <itemName> on siteaccess "site"
    And I reload the page
    Then I see correct preview data for "Folder" Content Type
      | field | value      |
      | title | <itemName> |
    And response headers contain
      | Header          | Value                  |
      | Cache-Control   | public, s-maxage=86400 |
      | X-Symfony-Cache | <headerValue>          |


    Examples:
      | user      | password | itemName                     | headerValue                                   |
      | admin     | publish  | TestFolderShortNameAdmin     | GET /site/testfoldershortnameadmin: fresh     |
      | anonymous |          | TestFolderShortNameAnonymous | GET /site/testfoldershortnameanonymous: fresh |

  @admin
  Scenario Outline: Cache is refreshed when item is edited
    Given I create "Folder" Content items in root in "eng-GB"
      | name       | short_name |
      | TestFolder | <itemName> |
    And I am viewing the pages on siteaccess "site" as "<user>" with password "<password>"
    And I visit "<itemName>" on siteaccess "site"
    And I see correct preview data for "Folder" Content Type
      | field | value      |
      | title | <itemName> |
    When I edit "<itemName>" Content item in "eng-GB"
      | short_name          |
      | <itemNameAfterEdit> |
    And I reload the page
    And I reload the page
    Then I see correct preview data for "Folder" Content Type
      | field | value               |
      | title | <itemNameAfterEdit> |
    And response headers contain
      | Header          | Value                  |
      | Cache-Control   | public, s-maxage=86400 |
      | X-Symfony-Cache | <headerValue>          |

    Examples:
      | user      | password | itemName        | itemNameAfterEdit | headerValue                      |
      | admin     | publish  | NameToEditAdmin | NameEditedAdmin   | GET /site/nameeditedadmin: fresh |
      | anonymous |          | NameToEditAnon  | NameEditedAnon    | GET /site/nameeditedanon: fresh  |
