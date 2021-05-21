@setup
Feature: Set system to desired state before tests
  
  @admin
  Scenario: Set up the system to test caching of subrequests
    Given I create a "embeddedContentType" Content Type in "Content" with "embeddedContentType" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	   | yes      | yes	       | yes          |
    And I create "embeddedContentType" Content items in root in "eng-GB"
      | name              |
      | EmbeddedItemNoEsi |
      | EmbeddedItemEsi   |
    And I create a "embeddingContentType_no_esi" Content Type in "Content" with "embeddingContentType_no_esi" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	   | yes      | yes	       | yes          |
      | Content relation (single) | Relation  | relation   | yes      | no	       | yes          |
    And I create "embeddingContentType_no_esi" Content items in root in "eng-GB"
      | name               | relation           |
      | EmbeddingItemNoEsi | /EmbeddedItemNoEsi |
    And I create a "embeddingContentType_esi" Content Type in "Content" with "embeddingContentType_esi" identifier
      | Field Type                | Name      | Identifier | Required | Searchable | Translatable |
      | Text line                 | Name      | name	   | yes      | yes	       | yes          |
      | Content relation (single) | Relation  | relation   | yes      | no	       | yes          |
    And I create "embeddingContentType_esi" Content items in root in "eng-GB"
      | name             | relation         |
      | EmbeddingItemEsi | /EmbeddedItemEsi |
    And I set configuration to "ezplatform.system.default.content_view"
    """
      full:
        embeddingContentType_no_esi:
            controller: EzSystems\BehatBundle\Controller\RenderController::embedAction
            template: "@eZBehat/tests/cache/embed_no_esi.html.twig"
            match:
                Identifier\ContentType: [embeddingContentType_no_esi]
        embeddingContentType_esi:
            controller: EzSystems\BehatBundle\Controller\RenderController::embedAction
            template: "@eZBehat/tests/cache/embed_esi.html.twig"
            match:
                Identifier\ContentType: [embeddingContentType_esi]
      line:
        embedded:
            controller: EzSystems\BehatBundle\Controller\RenderController::longAction
            template: "@eZBehat/tests/cache/embedded.html.twig"
            match:
                Identifier\ContentType: [embeddedContentType]
    """
