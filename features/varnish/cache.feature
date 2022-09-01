@varnish6 @varnish7
Feature: As an site administrator I want my pages to be cached using Varnish

    @admin
    Scenario Outline: Content Items are cached for user when visited
        Given I create "Folder" Content items in root in "eng-GB"
            | name       | short_name |
            | TestFolder | <itemName> |
        And I am viewing the pages on siteaccess "site" as "<user>" "<password>"
        When I visit <itemName> on siteaccess "site"
        And response headers contain
            | Header  | Value |
            | X-Cache | MISS  |
        And I reload the page
        Then I see correct preview data for "Folder" Content Type
            | field | value      |
            | title | <itemName> |
        And response headers contain
            | Header  | Value |
            | X-Cache | HIT   |

        Examples:
            | user      | password | itemName                     |
            | admin     | publish  | TestFolderShortNameAdmin     |
            | anonymous |          | TestFolderShortNameAnonymous |

    @admin
    Scenario Outline: Cache is refreshed when item is edited
        Given I create "Folder" Content items in root in "eng-GB"
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
            | admin     | publish  | NameToEditAdmin | NameEditedAdmin   |
            | anonymous |          | NameToEditAnon  | NameEditedAnon    |
