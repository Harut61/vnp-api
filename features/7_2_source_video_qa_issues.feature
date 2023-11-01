Feature: Manage SourceVideoQaIssues
  In order to manage SourceVideoQaIssues
  As a client software developer
  I need to be able to retrieve, create and update them through the API.

  Scenario: Create a Source Video Qa Issues TestQaIssue
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssues named TestQaIssue with:
    """
        {
            "source": "/sources/<SourceKRON4:id>",
            "issue_type": "/source_video_qa_issues_types/<QaIssueType2:id>",
            "comment": "Test comment"
        }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <TestQaIssue:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      }
    """

Scenario: Create a Source Video Qa Issues QaIssue1
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssues named QaIssue1 with:
    """
        {
            "source": "/sources/<SourceKRON4:id>",
            "issue_type": "/source_video_qa_issues_types/<QaIssueType2:id>",
            "comment": "Test comment"
        }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <QaIssue1:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      }
    """

Scenario: Create a Source Video Qa Issues QaIssue2
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssues named QaIssue2 with:
    """
        {
            "source": "/sources/<SourceKRON4:id>",
            "issue_type": "/source_video_qa_issues_types/<QaIssueType2:id>",
            "comment": "Test comment"
        }
    """
    Then the response status code should be 201
    And the JSON should be a smart superset of:
    """
      {
        "id": <QaIssue2:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      }
    """

Scenario: Create a Source Video Qa Issues EmptyQaIssue
    Given I am logged in as SuperAdmin
    And I smartly create the SourceVideoQaIssues named EmptyQaIssue with:
    """
        {
            "source": "/sources/<SourceKRON4:id>",
            "issue_type": "/source_video_qa_issues_types/<QaIssueType2:id>",
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

Scenario: Get all Source Video Qa Issues By Id Desc
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/source_video_qa_issues"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <QaIssue2:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      },
      {
        "id": <QaIssue1:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      },
      {
        "id": <TestQaIssue:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      }
    ]
    """

Scenario: Delete a Source Video Qa Issues
    Given I am logged in as SuperAdmin
    And I send a smart "DELETE" request to "/source_video_qa_issues/<TestQaIssue:id>"
    Then the response status code should be 204

Scenario: Verify if Source Video Qa Issue has been deleted
    Given I am logged in as SuperAdmin
    And I send a smart "Get" request to "/source_video_qa_issues"
    Then the response status code should be 200
    And the JSON should be a smart superset of:
    """
    [
      {
        "id": <QaIssue2:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      },
      {
        "id": <QaIssue1:id>,
        "source": {
            "id": <SourceKRON4:id>
        },
        "issue_type": { 
            "id": <QaIssueType2:id> 
        },
        "comment": "Test comment"
      }
    ]
    """