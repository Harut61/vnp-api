Feature: Manage StoryQaIssue
  In order to manage StoryQaIssue
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Story Qa Issues StoryTestQaIssue
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssue named StoryTestQaIssue with:
    """
        {
            "issue_type": "/story_qa_issues_types/<StoryQaIssueType1:id>",
            "comment": "Test comment"
        }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryTestQaIssue:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Test comment"
      }
    """

Scenario: Create a Story Qa Issues StoryQaIssue1
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssue named StoryQaIssue1 with:
    """
        {
            "issue_type": "/story_qa_issues_types/<StoryQaIssueType1:id>",
            "comment": "Issue One"
        }
    """

    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryQaIssue1:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue One"
      }
    """

Scenario: Create a Story Qa Issues StoryQaIssue2
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssue named StoryQaIssue2 with:
    """
        {
            "issue_type": "/story_qa_issues_types/<StoryQaIssueType1:id>",
            "comment": "Issue Two"
        }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <StoryQaIssue2:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue Two"
      }
    """

Scenario: Create a Story Qa Issues EmptyQaIssue
    Given I am logged in as SuperAdmin
    And I smartly create the StoryQaIssue named EmptyQaIssue with:
    """
        {
            "issue_type": "/story_qa_issues_types/<StoryQaIssueType1:id>",
            "comment": ""
        }
    """
    Then the response status code should be 422
    And the JSON should be a smart superset of:
    """
      {
        "violations": [
            {
                "propertyPath": "comment",
                "message": "This value should not be blank.",
                "code": "c1051bb4-d103-4f74-8988-acbcafc7fdc3"
            }
        ]
      }
    """

Scenario: Get all Story Qa Issues By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_qa_issues"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <StoryQaIssue2:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue Two"
      },
      {
        "id": <StoryQaIssue1:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue One"
      },
      {
        "id": <StoryTestQaIssue:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Test comment"
      }
    ]
    """

Scenario: Delete a Story Qa Issues
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/story_qa_issues/<StoryTestQaIssue:id>"
    Then the response status code should be 204

Scenario: Verify if Story Qa Issue has been deleted
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/story_qa_issues"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <StoryQaIssue2:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue Two"
      },
      {
        "id": <StoryQaIssue1:id>,
        "issue_type": { 
            "id": <StoryQaIssueType1:id> 
        },
        "comment": "Issue One"
      }
    ]
    """

