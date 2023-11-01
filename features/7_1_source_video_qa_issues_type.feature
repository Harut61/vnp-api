Feature: Manage SourceVideoQaIssuesType
  In order to manage SourceVideoQaIssuesType
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Source Video Qa Issues Type TestQaIssueType
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssuesType named TestQaIssueType with:
    """
      {
        "title": "Test issue type"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <TestQaIssueType:id>,
        "title": "Test issue type"
      }
    """

  Scenario: Create a Source Video Qa Issues Type QaIssueType1
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssuesType named QaIssueType1 with:
    """
      {
        "title": "Issue type 1"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <QaIssueType1:id>,
        "title": "Issue type 1"
      }
    """

  Scenario: Create a Source Video Qa Issues Type QaIssueType2
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssuesType named QaIssueType2 with:
    """
      {
        "title": "Issue type 2"
      }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <QaIssueType2:id>,
        "title": "Issue type 2"
      }
    """

  Scenario: Create a Source Video Qa Issues Type EmptyQaIssueType
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssuesType named EmptyQaIssueType with:
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

  Scenario: Get all Source Video Qa Issues Types By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/source_video_qa_issues_types"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <QaIssueType2:id>,
        "title": "Issue type 2"
      },
      {
        "id": <QaIssueType1:id>,
        "title": "Issue type 1"
      },
      {
        "id": <TestQaIssueType:id>,
        "title": "Test issue type"
      }
    ]
    """

  Scenario: Delete a Source Video Qa Issues Types
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/source_video_qa_issues_types/<TestQaIssueType:id>"
    Then the response status code should be 204

  Scenario: Verify if Source Video Qa Issues Type has been deleted
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/source_video_qa_issues_types"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <QaIssueType2:id>,
        "title": "Issue type 2"
      },
      {
        "id": <QaIssueType1:id>,
        "title": "Issue type 1"
      }
    ]
    """
