@varnish6 @varnish7
Feature: Cached response is different for users with different permissions

  Scenario: Content Items are cached based on users permissions when directly visited
    Given I am viewing the pages on siteaccess "site" as "admin" with password "publish"
    And I visit "Users/Administrator-users/Administrator-User" on siteaccess "site"
    And I reload the page
    And I should see "Administrator User"
    And response headers contain
      | Header  | Value |
      | X-Cache | HIT   |
    When I am viewing the pages on siteaccess "site" as "Anonymous"
    And I visit "Users/Administrator-users/Administrator-User" on siteaccess "site"
    Then I should not see "Administrator User"
    And the url should match "login"

  @admin
  Scenario: Embedded Content Items are cached based on users permissions
    Given I create a "testContentType" Content Type in "Content" with "testContentType" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	     | yes      | yes	       | yes          |
      | Content relation (single) | Relation  | relation   | yes      | no	       | yes          |
    And I create "testContentType" Content items in root in "eng-GB"
      | name            | relation                                       |
      | TestContentItem | /Users/Administrator-users/Administrator-User |
    Given I am viewing the pages on siteaccess "site" as "admin" with password "publish"
    And I visit "TestContentItem" on siteaccess "site"
    And I reload the page
    And I should see "TestContentItem"
    And I should see "Administrator User"
    And response headers contain
      | Header  | Value |
      | X-Cache | HIT   |
    When I am viewing the pages on siteaccess "site" as "Anonymous"
    And I visit "TestContentItem" on siteaccess "site"
    Then I should see "TestContentItem"
    And I should not see "Administrator User"
