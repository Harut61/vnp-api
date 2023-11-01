Feature: Manage StoryQaIssuesType
  In order to manage StoryQaIssuesType
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Story Qa Issues Type StoryTestQaIssueType
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssuesType named StoryTestQaIssueType with:
    """
      {
        "title": "Test issue type"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryTestQaIssueType:id>,
        "title": "Test issue type"
      }
    """

  Scenario: Create a Story Qa Issues Type StoryQaIssueType1
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssuesType named StoryQaIssueType1 with:
    """
      {
        "title": "Issue type 1"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryQaIssueType1:id>,
        "title": "Issue type 1"
      }
    """

  Scenario: Create a Story Qa Issues Type StoryQaIssueType2
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssuesType named StoryQaIssueType2 with:
    """
      {
        "title": "Issue type 2"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryQaIssueType2:id>,
        "title": "Issue type 2"
      }
    """

  Scenario: Create a Story Qa Issues Type EmptyQaIssueType
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssuesType named EmptyQaIssueType with:
    """
      {
        "title": ""
      }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
          {
            "propertyPath": "title",
            "message": "This value should not be blank.",
            "code": "c1051bb4-d103-4f74-8988-acbcafc7fdc3"
            }
        ]
      }
    """

  Scenario: Get all Story Qa Issues Types By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_qa_issues_types"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <StoryQaIssueType2:id>,
        "title": "Issue type 2"
      },
      {
        "id": <StoryQaIssueType1:id>,
        "title": "Issue type 1"
      },
      {
        "id": <StoryTestQaIssueType:id>,
        "title": "Test issue type"
      }
    ]
    """

  Scenario: Delete a Story Qa Issues Types
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/story_qa_issues_types/<StoryTestQaIssueType:id>"
    Then the response status code should be 204

  Scenario: Verify if Story Qa Issues Type has been deleted
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_qa_issues_types"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <StoryQaIssueType2:id>,
        "title": "Issue type 2"
      },
      {
        "id": <StoryQaIssueType1:id>,
        "title": "Issue type 1"
      }
    ]
    """
