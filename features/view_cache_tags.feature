Feature: HTTP cache tagging of views
Background:
    Given that view cache is enabled

Scenario: Viewing a content item tags the Response
  Given a Content item
   When I view this Content item
   Then the response is tagged with "c<contentId>"
    And the response is tagged with "l<locationId>"
    And the response is tagged with "ct<contentTypeId>"

Scenario: Viewing a particular location of a content tags the Response with that location's ID
  Given a Content item with a secondary Location
   When I view the Content item from that location
   Then the response is tagged with "l<secondaryLocationId>"

Scenario: Viewing a content item with a relation with the default field template tags the Response
  Given a Content item with a filled relation field
    And the default template is used to render relation fields
   When I view this content item
   Then the response is tagged with "r<relatedContentId>"
